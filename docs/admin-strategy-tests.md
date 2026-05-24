# Relatório de testes (Admin Strategy)

## Suite coberta

Testes unitários adicionados para validar:

- Regras de validação por entidade (incluindo `_validate_field` para validação em tempo real)
- Persistência (`create/update`) e efeitos colaterais esperados (ex.: hash de senha)

Arquivos:

- [ProductStrategyTest.php](file:///h:/Users/Gui/Desktop/Croche/backend/tests/Unit/Admin/Strategies/ProductStrategyTest.php)
- [CategoryStrategyTest.php](file:///h:/Users/Gui/Desktop/Croche/backend/tests/Unit/Admin/Strategies/CategoryStrategyTest.php)
- [UserStrategyTest.php](file:///h:/Users/Gui/Desktop/Croche/backend/tests/Unit/Admin/Strategies/UserStrategyTest.php)

## Como executar

No Windows, a execução requer extensões PHP habilitadas (especialmente `mbstring`).

Executar a suite Unit:

```powershell
cd backend
php -c ..\php.ini artisan test --testsuite=Unit
```

## Observação do ambiente

No ambiente atual de execução, a suite não roda por falta da extensão `mbstring` disponível no runtime do PHP. O erro apresentado foi:

```
PHPUnit requires the ... "mbstring" extension, but the "mbstring" extension is not available.
```

Ao rodar localmente com um PHP que tenha `mbstring` instalada/habilitada, a suite deve executar normalmente (os testes criam as tabelas necessárias em SQLite `:memory:`).

