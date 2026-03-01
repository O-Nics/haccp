# Documentation — Le Pattern Ensure

## Vue d'ensemble

Le pattern **Ensure** est la couche de **validation des règles métier** de l'application. Il intervient dans le **Service**, après l'autorisation (Policy) et la validation du payload (FormRequest).

## Positionnement dans le flux de traitement

| Couche | Responsabilité | Code HTTP en cas d'échec |
|---|---|---|
| **Policy** (Middleware) | L'utilisateur a-t-il **le droit** ? | `403 Forbidden` |
| **FormRequest** (Controller) | Les paramètres envoyés sont-ils **valides** ? | `422 Unprocessable Entity` |
| **Ensure** (Service) | L'action est-elle **métier-possible** ? | `409 Conflict` |
| **Repository** | Exécution de la persistence | — |

## Pourquoi ce pattern ?

**Problème** : Les règles métier se retrouvent dispersées dans les controllers, les services, voire les modèles. Cela rend le code difficile à maintenir et à tester.

**Solution** : Centraliser les vérifications métier dans des classes dédiées (`Ensure`) qui répondent à une seule question : **« Est-ce que c'est possible ? »**

## Principes

* **Une classe Ensure par entité métier** — `RdvEnsure`, `UserEnsure`, `PaymentEnsure`, etc.
* **Une méthode = une règle métier** — Nommée de façon lisible et déclarative.
* **Lève une `BusinessException` (409)** si la condition n'est pas remplie.
* **Message personnalisable** via le paramètre optionnel `$customMessage`.
* **Injectée dans les Services** via le constructeur (injection de dépendances).

## Anatomie d'un fichier Ensure

```php
class RdvEnsure extends BaseEnsure
{
    // Injection des dépendances nécessaires
    public function __construct(
        private readonly MangopayPayInRepositoryInterface $mangopayPayInRepository,
    ) {}

    // Chaque méthode vérifie UNE règle métier
    public function isNotEnded(Rdv $rdv, ?string $customMessage = null): void
    {
        if ($rdv->end_at->lessThan(now())) {
            $this->fail($customMessage ?? 'Le rendez-vous est déjà terminé.');
        }
    }
}
```

### Structure type d'une méthode Ensure

1. **Reçoit l'entité** concernée en paramètre
2. **Vérifie une condition** métier
3. **Appelle `$this->fail()`** avec un message explicite si la condition échoue
4. **Ne retourne rien** (`void`) — le succès est silencieux

## Exemple d'utilisation dans un Service

```php
class RdvService
{
    public function __construct(
        private readonly RdvEnsure $rdvEnsure,
    ) {}

    public function cancel(Rdv $rdv): void
    {
        // Règles métier chaînées — lisibles comme une checklist
        $this->rdvEnsure->isNotCanceled($rdv);
        $this->rdvEnsure->isNotEnded($rdv);
        $this->rdvEnsure->paymentNotTransferred($rdv);

        // Si on arrive ici, tout est OK → on exécute
        $rdv->cancel();
    }
}
```

## Ce que fait un Ensure vs ce qu'il ne fait PAS

| ✅ Ensure fait | ❌ Ensure ne fait pas |
|---|---|
| Vérifier l'état d'une ressource (annulé ? terminé ?) | Vérifier les permissions utilisateur → **Policy** |
| Vérifier les contraintes métier (assez de places ?) | Valider le format des données → **FormRequest** |
| Vérifier la cohérence business (promo applicable ?) | Persister en base de données → **Repository** |
| Vérifier l'existence d'un service externe | Orchestrer la logique → **Service** |

## Conventions de nommage

| Pattern de nom | Usage | Exemple |
|---|---|---|
| `isNot{State}` | Vérifier qu'une entité **n'est pas** dans un état | `isNotCanceled`, `isNotEnded` |
| `is{State}` | Vérifier qu'une entité **est** dans un état | `isEnded`, `isOnline` |
| `hasNo{Thing}` | Vérifier l'**absence** d'une relation | `hasNoPaymentOrInvoice` |
| `doesNotRequire{Thing}` | Vérifier qu'une contrainte **ne s'applique pas** | `doesNotRequireOnlinePayment` |
| `{subject}Not{State}` | Vérifier qu'un élément lié n'est pas dans un état | `paymentNotTransferred` |

## Résumé

> **Un Ensure répond à la question : « Est-ce que l'état actuel du système permet cette action ? »**
> Si non → `BusinessException` (409). Si oui → on continue silencieusement.

