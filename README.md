# Bitrix Budget Estimate Component

Custom Bitrix component for creating and managing budget estimates for grant applications.

The component allows users to build structured expense tables with dynamic rows, automatic calculations, category totals, and JSON-based storage in Bitrix properties.

#Note
This component is designed for Bitrix CMS and requires a Bitrix environment to run.
The repository focuses on demonstrating component architecture and frontend logic.

## Features

- Dynamic estimate table with categories
- Add / remove rows via JavaScript
- Automatic calculation of row totals
- Automatic calculation of category totals
- Create new estimates
- Edit existing estimates
- Automatic loading of estimate by application ID
- Structured data storage in JSON
- Saving estimates as Bitrix IBlock elements

## Component Parameters

The component can be configured through external parameters when included in a Bitrix page.

| Parameter | Description |
|----------|-------------|
| IBLOCK_ZAYAVKA | ID of the IBlock that stores grant applications |
| IBLOCK_SMETA | ID of the IBlock where estimates are stored |
| PRESET_CAT | Array of predefined estimate categories used to generate default rows |

### Example Component Call

```php
$APPLICATION->IncludeComponent(
    "anr:smeta",
    "",
    [
        "IBLOCK_ZAYAVKA" => 12,
        "IBLOCK_SMETA" => 23,
        "PRESET_CAT" => [
		"Category 1",
		["Category 21", "Category 22"],
		["Category 31", "Category 32", "Category 33", "Category 33"]
		]
    ]
);
```markdown
PRESET_CAT allows the component to receive a predefined structure of estimate categories.
These categories are used to generate default rows when creating a new estimate.

## Technologies

- PHP
- Bitrix CMS
- JavaScript (vanilla)
- HTML / CSS
- JSON data storage

## Architecture

The component follows a typical Bitrix structure:
/local/components/anr/smeta/
component.php
result_modifier.php
save.php
templates/
.template/
template.php
script.js


### Backend

The PHP logic:

- processes submitted form data
- converts table data into a structured array
- encodes it to JSON
- saves it to a Bitrix IBlock property

Estimate structure example:
[{
"rows": [
  {
  "name": "Equipment purchase",
  "cols": {
    "1": 5000,
    "2": 2,
    "3": 10000
    }
  }
],
"comment": "Equipment expenses"
}]

### Frontend

JavaScript provides dynamic behaviour:

- row creation and deletion
- automatic multiplication of values in a row
- automatic calculation of category totals
- form submission protection (prevent double submit)

### Data Loading

The component checks if an estimate already exists for the given application ID.

If an estimate is found:
- stored JSON data is decoded
- table rows are reconstructed dynamically

If no estimate exists:
- the component generates default rows from predefined categories

## Example Workflow

1. User opens the estimate page with an application ID
2. The component checks if an estimate already exists
3. If found, the estimate data is loaded and displayed for editing
4. If not, a new estimate form is created
5. User edits rows or adds new expenses
6. Row totals and category totals are calculated automatically
7. User submits the form
8. Data is saved or updated in the Bitrix IBlock

## Purpose

This component was developed for a grant application system where applicants must submit detailed project budgets.

The solution replaces static forms with a dynamic and structured estimate editor.

## Author

Liudmila Prikhodko
