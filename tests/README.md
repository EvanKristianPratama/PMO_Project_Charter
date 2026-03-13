# Test Foldering

## Structure

- `tests/Pure`: pure unit tests with no database or framework bootstrapping requirement.
- `tests/Integration/UnitModels`: backend integration tests for model behaviors.
- `tests/Integration/Feature`: backend feature tests (HTTP/auth/permissions).

## PHPUnit Suites

- `PureUnit` -> `tests/Pure`
- `BackendIntegration` -> `tests/Integration/UnitModels`
- `BackendFeature` -> `tests/Integration/Feature`

## Notes

- CI runs `PureUnit` as mandatory gate.
- Integration and feature suites run when migration files are available.
- Frontend tests run using Node test runner from `resources/js/tests`.
