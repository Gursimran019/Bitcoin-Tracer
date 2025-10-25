# Design Document

## Overview

The transaction list feature will add a "List" button to the main interface that displays all transactions from the current graph in a comprehensive table format. This feature leverages the existing transaction data infrastructure and provides an alternative view to complement the visual graph representation.

## Architecture

### Current System Architecture
The application follows a client-server architecture:
- **Frontend**: HTML/CSS/JavaScript with vis.js for graph visualization
- **Backend**: PHP API with PostgreSQL database
- **Data Flow**: API endpoints serve transaction data in JSON format
- **Styling**: CSS custom properties for theme support (dark/light mode)

### Integration Approach
The list feature will integrate seamlessly with the existing architecture by:
1. Adding a new button to the existing search-group interface
2. Reusing the existing transaction data fetching mechanisms
3. Implementing an overlay-style list view similar to the existing transaction list functionality
4. Maintaining consistency with current styling and theming

## Components and Interfaces

### 1. User Interface Components

#### List Button Component
- **Location**: Added to `.search-group` alongside existing buttons
- **Styling**: Consistent with existing button styles using CSS custom properties
- **Icon**: Font Awesome list icon (`fas fa-list`)
- **Behavior**: Shows/hides the transaction list overlay

#### Transaction List Overlay
- **Container**: `#transactionList` - Fixed position overlay
- **Structure**:
  - Header section with title and navigation buttons
  - Table container with scrollable content
  - Loading and error state displays

#### Navigation Controls
- **Back to Graph Button**: Returns to main graph view
- **Print Button**: Triggers browser print dialog with print-optimized styling

### 2. Data Components

#### Transaction Data Structure
```javascript
{
  id: string,           // Transaction ID
  from: array,          // Input addresses
  to: array,            // Output addresses
  total_input: string,  // Total input amount
  total_output: string, // Total output amount
  fees: string,         // Transaction fees
  date: string          // Formatted timestamp
}
```

#### API Integration
- **Endpoint**: Reuse existing `api.php` with `action=list` parameter
- **Data Source**: `getSampleTransactions()` function for all graph transactions
- **Response Format**: JSON array of transaction objects
- **Error Handling**: Standardized error responses with appropriate HTTP status codes

### 3. State Management

#### View State
- **Graph View**: Default state showing the transaction graph
- **List View**: Overlay state showing the transaction table
- **Loading State**: Displayed during API calls
- **Error State**: Displayed when API calls fail

#### Data State
- **Transaction Cache**: Store fetched transactions to avoid redundant API calls
- **Sort Order**: Transactions sorted by timestamp (chronological order)
- **Filter State**: Future extensibility for filtering capabilities

## Data Models

### Transaction Model
```javascript
class Transaction {
  constructor(data) {
    this.id = data.id;
    this.from = Array.isArray(data.from) ? data.from : [data.from];
    this.to = Array.isArray(data.to) ? data.to : [data.to];
    this.totalInput = data.total_input || 'N/A';
    this.totalOutput = data.total_output || 'N/A';
    this.fees = data.fees || 'N/A';
    this.date = data.date || 'N/A';
  }
  
  formatForDisplay() {
    return {
      id: this.id,
      fromAddresses: this.from.join(', '),
      toAddresses: this.to.join(', '),
      totalInput: this.totalInput,
      totalOutput: this.totalOutput,
      fees: this.fees,
      formattedDate: this.date
    };
  }
}
```

### API Response Model
```javascript
// Success Response
{
  data: Transaction[],
  count: number,
  timestamp: string
}

// Error Response
{
  error: string,
  code: number,
  details?: string
}
```

## Error Handling

### Client-Side Error Handling
1. **Network Errors**: Display connection error message with retry option
2. **API Errors**: Show specific error messages from server responses
3. **Data Validation**: Handle malformed or missing transaction data gracefully
4. **Loading States**: Provide visual feedback during data fetching

### Server-Side Error Handling
1. **Database Connection**: Proper PDO exception handling
2. **Query Failures**: Detailed error logging with user-friendly messages
3. **Data Formatting**: Fallback values for missing or invalid data
4. **HTTP Status Codes**: Appropriate status codes for different error types

### Error Display Strategy
- **Loading State**: "Loading transactions..." with spinner
- **No Data**: "No transactions found" message
- **Network Error**: "Failed to load data" with retry button
- **Server Error**: "Server error occurred" with technical details in console

## Testing Strategy

### Unit Testing
1. **Data Transformation**: Test transaction data parsing and formatting
2. **API Integration**: Mock API responses and test error handling
3. **UI Components**: Test button interactions and state changes
4. **Utility Functions**: Test address parsing and date formatting

### Integration Testing
1. **API Endpoints**: Test actual API calls with various parameters
2. **Database Queries**: Verify transaction retrieval and sorting
3. **Cross-Browser**: Test functionality across different browsers
4. **Responsive Design**: Test layout on different screen sizes

### User Acceptance Testing
1. **Navigation Flow**: Test switching between graph and list views
2. **Data Accuracy**: Verify transaction data matches between views
3. **Print Functionality**: Test print layout and button hiding
4. **Performance**: Test with large transaction datasets
5. **Accessibility**: Test keyboard navigation and screen reader compatibility

### Test Data Requirements
- **Sample Transactions**: Minimum 50 transactions with varied data
- **Edge Cases**: Empty addresses, missing dates, zero amounts
- **Large Datasets**: Test performance with 1000+ transactions
- **Error Scenarios**: Network failures, invalid responses, empty datasets

## Implementation Considerations

### Performance Optimization
1. **Data Caching**: Cache transaction data to avoid redundant API calls
2. **Lazy Loading**: Consider pagination for large transaction sets
3. **DOM Optimization**: Efficient table rendering for large datasets
4. **Memory Management**: Proper cleanup when switching views

### Accessibility Features
1. **Keyboard Navigation**: Tab order and focus management
2. **Screen Reader Support**: Proper ARIA labels and descriptions
3. **High Contrast**: Ensure readability in different themes
4. **Focus Indicators**: Clear visual focus states for interactive elements

### Browser Compatibility
- **Modern Browsers**: Chrome, Firefox, Safari, Edge (latest versions)
- **JavaScript Features**: ES6+ features with appropriate fallbacks
- **CSS Features**: CSS Grid/Flexbox with fallback layouts
- **Print Support**: Cross-browser print CSS compatibility

### Security Considerations
1. **XSS Prevention**: Proper HTML escaping for transaction data
2. **CSRF Protection**: Maintain existing CSRF protections
3. **Input Validation**: Validate all user inputs and API responses
4. **Error Information**: Avoid exposing sensitive system information in errors