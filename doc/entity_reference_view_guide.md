# Entity Reference View Guide for Drupal

This guide explains how to use an Entity Reference View so that a Booking content type can select from available Car Details nodes.

## Step 1: Create the Entity Reference View

1. In the Drupal admin, go to:
   - `Structure` → `Views`
2. Add a new view.
3. Configure the view as follows:
   - View name: `Car View`
   - Show: `Content`
   - Of type: `Car Details`
   - Create a page: `No`
   - Create a block: `No`
4. After saving the view, add a new display.
   - Display type: `Entity Reference`
5. Save the view.

The view should return nodes of type `Car Details`.

## Step 2: Create the Entity Reference Field

1. In Drupal admin, navigate to:
   - `Structure` → `Content types` → `Booking` → `Manage fields`
2. Add field:
   - Field type: `Reference` → `Content Reference (Entity Reference)`
   - Label: `Car`
   - Machine name: `field_car`
3. Save field settings.

## Step 3: Configure the Reference Method

1. Edit the field settings for `field_car`.
2. Locate the `Reference method` setting.
3. Choose:
   - `Views: Filter by an entity reference view`
4. Save the field settings.

## Step 4: Select Your View

1. After selecting the Views reference method, Drupal displays a view selector.
2. Choose:
   - View: `Car View`
   - Display: `Entity Reference`
3. Save the settings.

Now the `Car` field is linked to the `Car View` entity reference display.

## Step 5: Test

1. Go to:
   - `Content` → `Add content` → `Booking`
2. In the `Car` field dropdown, you should see available car titles such as:
   - `Toyota Innova`
   - `Honda City`
   - `Hyundai Creta`

These values are provided by the Entity Reference View.

## Why Use Entity Reference Views?

### Filter Records

You can show only specific cars by adding filters to the view.

Examples:
- `Content: Published = Yes`
- `Car Status = Available`
- `Car Type = SUV`
- `Car Price < 1000000`

Only matching cars appear in the entity reference field.

### Sort Records

You can sort the list of car choices using view sort criteria.

Example:
- Sort criteria: `Car Price ASC`

This ensures lower-priced cars appear first in the selection list.

### Display More Information

The entity reference display can show more than just the title.

Example fields in the view display:
- Title
- Car Color
- Car Model
- Car Price
- Car Type

Then the dropdown can show results like:
- `Toyota Innova | White | SUV | ₹18,00,000`
- `Honda City | Red | Sedan | ₹12,00,000`

This makes it easier for users to choose the correct car.

## Example Use Case

- `Content Type: Car Details`
  - Title
  - Car Model
  - Car Color
  - Car Price
  - Car Type

- `Content Type: Booking`
  - Customer Name
  - Car (`field_car` entity reference)

With the Entity Reference View:
- Only available and published cars show up
- The list is sorted by price or title
- Users can see rich car details while selecting
