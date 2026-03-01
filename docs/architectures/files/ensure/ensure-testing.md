# Tests unitaires — Le Pattern Ensure

## Principes

Les tests Ensure sont des **tests unitaires purs** : aucune base de données, aucun framework de factory. Tout est instancié en mémoire.

Chaque test vérifie qu'une méthode Ensure lève bien une `BusinessException` quand la règle métier n'est pas respectée, et qu'elle ne lève rien quand elle l'est.

---

## Structure des fichiers

### Un fichier de test par méthode

Chaque méthode de la classe Ensure a son propre fichier de test.

```
tests/Unit/Ensure/RdvEnsure/
├── RdvEnsureIsOnlineTest.php
├── RdvEnsureIsNotStartedTest.php
├── RdvEnsureIsNotEndedTest.php
├── RdvEnsureIsNotCanceledTest.php
├── RdvEnsureIsEndedTest.php
├── RdvEnsureHasNoPaymentOrInvoiceTest.php
├── RdvEnsureDoesNotRequireOnlinePaymentTest.php
└── RdvEnsurePaymentNotTransferredTest.php
```

**Convention de nommage** : `{ClassName}{MethodName}Test.php`

---

## Anatomie d'un fichier de test

### Le `beforeEach` — instanciation avec mocks

La classe Ensure est instanciée avec ses dépendances **mockées**, accessibles comme propriétés de test.

```php
beforeEach(function () {
    $this->rdvEnsure = new RdvEnsure(
        $this->repository = mock(MangopayPayInRepositoryInterface::class),
        $this->logger = mock(LoggerInterface::class),
    );
});
```

> Même si tous les tests du fichier n'utilisent pas le repository ou le logger, ils sont toujours déclarés dans le `beforeEach` pour uniformité.

### Les 3 sections d'un test

Chaque test est découpé en 3 sections commentées dans cet ordre :

```php
it('description du cas', function () {
    // Data
    // ...construction du modèle

    // Mock
    // ...configuration des mocks (si des dépendances sont appelées)

    // Test
    // ...appel de la méthode Ensure
});
```

La section `// Mock` est **omise** si aucune dépendance injectée n'est appelée dans ce cas précis.

---

## Construire les données de test

### Attributs du modèle — `setRawAttributes()`

Pour simuler les attributs d'un modèle Eloquent **sans passer par la base de données** :

```php
$rdv = (new Rdv)
    ->setRawAttributes([
        'type' => 'online',
    ]);
```

Ne mettre que les attributs **nécessaires à la règle testée**. Pas de valeurs par défaut superflues.

### Relations Eloquent — `setRelation()`

Pour simuler des relations chargées sur le modèle :

```php
$rdv = (new Rdv)->setRawAttributes([]);
$rdv->setRelation('mangopayPayins', collect([new MangopayPayin]));
$rdv->setRelation('externalInvoice', null);
```

Toujours initialiser explicitement chaque relation utilisée par la méthode Ensure, même à `null` ou `collect([])`.

---

## Les assertions

### Cas d'échec — la règle métier n'est pas respectée

```php
it('return exception when rdv is canceled', function () {
    // Data
    $rdv = (new Rdv)
        ->setRawAttributes(['canceled_at' => now()]);

    // Test
    $this->rdvEnsure->isNotCanceled($rdv);
})->throws(BusinessException::class);
```

### Cas de succès — la règle métier est respectée

```php
it('return no exception when rdv is not canceled', function () {
    // Data
    $rdv = (new Rdv)
        ->setRawAttributes(['canceled_at' => null]);

    // Test
    $this->rdvEnsure->isNotCanceled($rdv);
})->throwsNoExceptions();
```

> Chaque méthode Ensure doit avoir **au minimum un test d'échec et un test de succès**. Si plusieurs conditions peuvent déclencher l'échec, chacune a son propre cas.

---

## Mocker les dépendances

### Repository

Quand la méthode Ensure appelle le repository, le mock est configuré dans la section `// Mock` :

```php
it('return exception when payment is already transferred', function () {
    // Data
    $rdv = (new Rdv)->setRawAttributes([]);

    // Mock
    $this->repository->shouldReceive('rdvHasPayinAlreadyTransferred')->with($rdv)->andReturn(true);
    $this->logger->shouldReceive('channel')->once()->andReturnSelf();
    $this->logger->shouldReceive('warning')->once()->andReturnSelf();

    // Test
    $this->rdvEnsure->paymentNotTransferred($rdv);
})->throws(BusinessException::class);
```

```php
it('return no exception when payment is not transferred', function () {
    // Data
    $rdv = (new Rdv)->setRawAttributes([]);

    // Mock
    $this->repository->shouldReceive('rdvHasPayinAlreadyTransferred')->with($rdv)->andReturn(false);

    // Test
    $this->rdvEnsure->paymentNotTransferred($rdv);
})->throwsNoExceptions();
```

### Logger

Le logger est mocké **uniquement dans les cas d'échec** qui déclenchent un log. Dans le cas de succès, le logger n'est pas configuré — Mockery vérifie implicitement qu'il n'est pas appelé.

```php
$this->logger->shouldReceive('channel')->once()->andReturnSelf();
$this->logger->shouldReceive('warning')->once()->andReturnSelf();
```

---

## Checklist pour un nouveau fichier de test Ensure

- [ ] Le fichier est nommé `{ClassName}{MethodName}Test.php`
- [ ] Le fichier est placé dans `tests/Unit/Ensure/{ClassName}/`
- [ ] Un `beforeEach` instancie la classe Ensure avec toutes ses dépendances mockées
- [ ] Il y a au moins un cas `->throws(BusinessException::class)`
- [ ] Il y a au moins un cas `->throwsNoExceptions()`
- [ ] Chaque condition d'échec distincte a son propre test
- [ ] Les données sont construites avec `setRawAttributes()` / `setRelation()` — pas de factory ni de DB
- [ ] Les sections `// Data`, `// Mock`, `// Test` sont présentes et dans l'ordre
- [ ] La section `// Mock` est omise si aucune dépendance n'est appelée
- [ ] Le logger est mocké uniquement dans les cas où un warning est émis