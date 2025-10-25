# Implementation Plan

- [x] 1. Remove existing Time Interval button and related functionality



  - Remove the Time Interval button from the HTML interface
  - Remove the `showTimeIntervalView()` function and related JavaScript code
  - Remove Time Interval button event listeners
  - Clean up any CSS styles specific to the Time Interval button

  - _Requirements: 1.1, 1.2_

- [ ] 2. Add context-sensitive Date & Time button to transaction list header
  - Add Date & Time button HTML element next to the print button (initially hidden)
  - Style the button to match existing UI design patterns
  - Implement `showDateTimeButton()` function to display button after successful search

  - Implement `hideDateTimeButton()` function to hide button when no search results
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [x] 3. Create date range selection interface overlay


  - Build HTML structure for date/time filter overlay with datetime-local inputs
  - Create CSS styling for the overlay to match existing modal patterns
  - Add form elements for Start Date & Time and End Date & Time inputs
  - Add View Type section with 'List' and 'Graph' radio buttons (initially hidden)
  - Include Apply Filter, Cancel, and Clear Filter buttons
  - _Requirements: 3.1, 3.2, 3.3, 3.5, 3.6, 3.7_

- [ ] 4. Implement date range interface show/hide functionality
  - Create `showDateFilterInterface()` function to display the overlay
  - Create `hideDateFilterInterface()` function to close the overlay
  - Add click handler for Date & Time button to open interface
  - Add click handlers for Cancel and overlay background to close interface
  - Implement keyboard support (Escape key to close)
  - _Requirements: 3.1, 3.2, 3.5, 3.6, 3.7_



- [ ] 5. Implement View Type visibility and interaction logic
  - Create `showViewTypeOptions()` function to display View Type section
  - Create `hideViewTypeOptions()` function to hide View Type section
  - Implement `checkBothDatesProvided()` function to detect when both dates are filled
  - Add event listeners to date inputs to toggle View Type visibility
  - Set 'List' as default selection when View Type becomes visible
  - Add event handlers for View Type radio button selection
  - _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5, 6.6_

- [ ] 6. Add date range validation and user feedback
  - Implement `validateDateRange()` function to check end date is after start date
  - Add real-time validation feedback for date inputs
  - Enable/disable Apply Filter button based on validation state
  - Display error messages for invalid date ranges
  - Add loading indicators for form submission
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 7.3, 7.5_


- [ ] 7. Integrate date filtering with existing API and view selection
  - Modify search result handlers to store current search ID for filtering
  - Create `fetchFilteredTransactions()` function to call API with date parameters and view type
  - Format datetime-local values to API-compatible format (Unix timestamps)
  - Implement logic to handle cases where only Start Date is provided (no End Date filter, list view)
  - Implement logic to handle cases where only End Date is provided (list view)
  - Implement logic to handle both dates with selected view type (List or Graph)
  - Handle API response and route to appropriate display method based on view type
  - Implement caching of original results for filter clearing
  - _Requirements: 4.5, 5.2, 5.3, 5.4, 5.5, 5.6_






- [ ] 8. Implement List and Graph view rendering
  - Create `renderListView()` function to display filtered transactions as a list
  - Create `renderGraphView()` function to display filtered transactions as a Neo4j-style graph
  - Ensure Graph view uses same styling and layout as main graph
  - Implement nodes for wallet addresses and edges for transactions in Graph view
  - Add interaction capabilities to Graph view (click, hover, zoom, pan)
  - Ensure both views handle empty result sets appropriately
  - _Requirements: 5.4, 5.5_

- [ ] 9. Implement comprehensive error handling and user feedback
  - Add error handling for API failures during date filtering
  - Display user-friendly error messages for network issues
  - Show "No transactions found in selected date range" message for empty results
  - Implement loading states during API calls
  - Add success feedback showing filtered transaction count
  - Clear previous error messages on successful operations
  - _Requirements: 6.1, 6.2, 6.4, 6.5_

- [ ] 10. Add Clear Filter functionality and state management
  - Implement Clear Filter button to restore original search results
  - Create state management for tracking filter status, view type, and original results
  - Update UI to indicate when date filter is active
  - Ensure proper cleanup when starting new searches
  - Add visual indicators showing current filter status
  - Reset View Type selection when clearing filters
  - _Requirements: 5.6, 7.5_