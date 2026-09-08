# Release Notes

## Unreleased

- Target Nova 5.9 and its current Nova devtool build stack.
- Support Spatie Laravel Permission 6.25+, 7.x, and 8.3+.
- Use Spatie's canonical permission migration and publish an additive `group` migration.
- Fix grouped checkbox serialization and group selection state on Nova 5.
- Scope permissions and unique-name validation by guard.
- Make the example seeder repeatable and use Nova's `forceDelete` policy ability.
- Fix custom menu disabling and remove obsolete empty tool routes.

## 1.7.0 (2024-12-27)

- Add support for Laravel Nova 5
- Drop support for Laravel Nova 4 and lower

## 1.6.2 (2024-07-31)

- Add setter methods for Role and Permission policies.
- Add cache for `permissions` and `users count` query on Role resource, improving index performance.
- Fixed an issue when listing the Roles when Preventing Lazy Loading is active.

## 1.6.1 (2024-05-06)

- Add namespace to seeders.
- Fix issue where permission menu item did not respect `disablePermissions`.

## 1.6.0 (2024-03-19)

- Added `disableMenu` to allow handling custom menu.

## 1.5.0 (2024-02-06)

- Fixed an issue where permission and role resources was not displayed in custom menu.

## 1.4.0 (2023-11-17)

- Added support for "spatie/laravel-permission" 6.0.

## 1.3.2 (2023-08-20)

- Fixed issue publishing `seeders`.

## v1.3.1 (2023-06-01)

## v1.3.0 (2023-04-27)

- Role resource now uses `permission.models.role` configuration as resource model.

## v1.2.4 (2023-03-05)

- Fixed an issue where permissions field were showing duplicated values.

## v1.2.3 (2023-02-28)

- Menu section now is collapsable.

## v1.2.2 (2023-02-18)

- Fixed deprecated static trait property.

## v1.2.1 (2023-01-26)

- The `isSuperAdmin` user method now is optional.
- Disabled permissions global search.
- Fixed an issue where permissions field were not showing correctly.

## v1.2.0 (2023-01-20)

- Added ability to customize role and permission resources.

## v1.1.0 (2022-12-19)

- Fixed issues with model for guard.
