# UAT Checklist

This checklist contains high-level scenarios for User Acceptance Testing.

1. Add a new customer
   - Go to Admin -> Customers -> Create
   - Verify customer appears in list

2. Place an order (Admin)
   - Admin -> Orders -> Create New Order
   - Add items, set delivery date, record advance
   - Verify order appears in Orders list
   - Verify customer can view order in `customer/my_orders.php`

3. Assign order to employee
   - Admin -> Orders -> View -> Assign to employee
   - Employee logs in -> Employee -> My Orders -> sees assigned order

4. Production and Stock
   - From Order View, go to Production -> produce
   - If stock available, complete production -> stock quantities decrease
   - Low stock alerts appear if thresholds are crossed

5. Payments
   - Record a payment on an order
   - Verify payments table and order balance update

6. Reports
   - Generate monthly revenue and verify totals

7. Security
   - Attempt to access admin pages as customer/employee -> should be denied
   - Test session timeout after inactivity

8. Edge cases
   - Negative quantity/product not found
   - Empty forms

Save results and record any bugs or inconsistencies.
