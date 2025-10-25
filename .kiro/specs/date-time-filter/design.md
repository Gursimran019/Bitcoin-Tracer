# Design Document

## Overview

The Date & Time filtering system replaces the existing Time Interval button with a more flexible date range filtering capability. The system provides context-sensitive filtering that appears only after successful transaction searches and supports various date range scenarios including open-ended filtering from a start date onward.

## Architecture

The system follows a modular approach with clear separation of concerns:

- **UI Layer**: Modal overlay with date input controls
- **Validation Layer**: Client-side date range validation
- **API Integration Layer**: Handles communication with existing transaction API
- **State Management**: Tracks filter state and original results for restoration

## Components and Interfaces

### UI Components

#### Date & Time Button
- Location: Transaction list header, next to print button
- Visibility: Context-sensitive (hidden by default, shown after successful searches)
- Styling: Matches existing UI design patterns

#### Date Filter Modal Overlay
- **Structure**: Modal with form inputs and action buttons
- **Inputs**: 
  - Start Date & Time (datetime-local input)
  - End Date & Time (datetime-local input, optional)
  - View Type (radio buttons: 'List' and 'Graph', visible only when both dates are filled)
- **Actions**: Apply Filter, Cancel, Clear Filter buttons
- **Behavior**: Closes on Cancel, Escape key, or background click

### JavaScript Functions

#### Core Functions
```javascript
// UI Management
showDateTimeButton()
hideDateTimeButton()
showDateFilterInterface()
hideDateFilterInterface()
showViewTypeOptions()
hideViewTypeOptions()

// Validation
validateDateRange(startDate, endDate)
checkBothDatesProvided(startDate, endDate)

// API Integration
fetchFilteredTransactions(searchId, startDate, endDate, viewType)

// View Management
renderListView(transactions)
renderGraphView(transactions)

// State Management
storeOriginalResults(results)
restoreOriginalResults()
```

#### Date Range Handling Logic
The system handles three main scenarios:

1. **Both dates provided**: Show View Type options, filter transactions between start and end dates, display in selected format (List or Graph)
2. **Only start date provided**: Hide View Type options, show all transactions from start date onward in list format
3. **Only end date provided**: Hide View Type options, show all transactions up to end date in list format

#### View Type Selection Logic
```javascript
// Show/hide View Type based on date inputs
function toggleViewTypeVisibility(startDate, endDate) {
  if (startDate && endDate) {
    showViewTypeOptions();
  } else {
    hideViewTypeOptions();
  }
}

// Handle view type selection
function handleViewTypeChange(viewType) {
  // Store selected view type for filter application
  currentViewType = viewType;
}
```

## Data Models

### Filter Parameters
```javascript
{
  searchId: string,        // Current search identifier
  startDate: string|null,  // ISO datetime string or null
  endDate: string|null,    // ISO datetime string or null
  viewType: string,        // 'list' or 'graph' (default: 'list')
  originalResults: array   // Cached original search results
}
```

### API Request Format
```javascript
{
  searchId: "existing_search_id",
  startTimestamp: number|null,  // Unix timestamp or null
  endTimestamp: number|null     // Unix timestamp or null
}
```

## Error Handling

### Validation Errors
- **Invalid date range**: End date before start date
- **Malformed dates**: Invalid datetime-local input values
- **UI feedback**: Real-time validation with error messages and button state management

### API Errors
- **Network failures**: Display user-friendly error messages
- **Empty results**: Show "No transactions found in selected date range" message
- **Server errors**: Handle gracefully with appropriate user feedback

### Loading States
- **Filter application**: Show loading indicators during API calls
- **Button states**: Disable Apply Filter button during processing
- **Progress feedback**: Clear visual indication of ongoing operations

## Testing Strategy

### Unit Tests
- Date validation logic
- Date format conversion (datetime-local to Unix timestamp)
- State management functions
- Error handling scenarios

### Integration Tests
- API integration with date parameters
- UI interaction flows (open/close modal, apply filters)
- Context-sensitive button visibility
- Filter clearing and result restoration

### User Acceptance Tests
- Complete filtering workflows
- Edge cases (empty dates, invalid ranges)
- Cross-browser datetime-local input compatibility
- Keyboard navigation and accessibility

## Implementation Notes

### Date Handling Specifics
- **Input format**: HTML5 datetime-local inputs provide consistent UX
- **API format**: Convert to Unix timestamps for backend compatibility
- **Timezone handling**: Use local timezone for user inputs, convert appropriately for API

### State Management Strategy
- Cache original search results to enable filter clearing
- Track current filter state to provide appropriate UI feedback
- Clean up state when new searches are initiated

### Performance Considerations
- Minimize API calls by validating inputs before submission
- Cache original results to avoid re-fetching when clearing filters
- Use efficient DOM manipulation for UI updates

### Accessibility
- Proper ARIA labels for modal overlay
- Keyboard navigation support (Tab, Escape)
- Screen reader compatible error messages
- Focus management when opening/closing modal