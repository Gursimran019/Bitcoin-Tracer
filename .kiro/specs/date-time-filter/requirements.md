# Requirements Document

## Introduction

This feature replaces the existing Time Interval button with a more flexible Date & Time filtering system that allows users to filter transaction results by specific date ranges. The system provides context-sensitive filtering that only appears after successful transaction searches and supports various date range scenarios including open-ended filtering.

## Requirements

### Requirement 1: Remove Time Interval Button

**User Story:** As a user, I want the old Time Interval button removed so that I can use the new Date & Time filtering system instead.

#### Acceptance Criteria

1. WHEN the application loads THEN the Time Interval button SHALL NOT be visible
2. WHEN searching for transactions THEN the Time Interval functionality SHALL NOT be available
3. WHEN the page is rendered THEN all Time Interval related code SHALL be removed

### Requirement 2: Context-Sensitive Date & Time Button

**User Story:** As a user, I want to see a Date & Time filter button only when I have transaction results so that I can filter those results by date range.

#### Acceptance Criteria

1. WHEN no search has been performed THEN the Date & Time button SHALL be hidden
2. WHEN a successful transaction search returns results THEN the Date & Time button SHALL be visible
3. WHEN a search returns no results THEN the Date & Time button SHALL remain hidden
4. WHEN the Date & Time button is clicked THEN the date range selection interface SHALL open

### Requirement 3: Date Range Selection Interface

**User Story:** As a user, I want an intuitive date range selection interface so that I can specify the time period for filtering transactions.

#### Acceptance Criteria

1. WHEN the Date & Time button is clicked THEN a modal overlay SHALL appear
2. WHEN the overlay is displayed THEN it SHALL contain Start Date & Time and End Date & Time input fields
3. WHEN both Start Date and End Date are filled THEN a 'View Type' option SHALL appear with 'List' and 'Graph' radio button choices
4. WHEN only one date field is filled OR both are empty THEN the 'View Type' option SHALL be hidden
5. WHEN the overlay is displayed THEN it SHALL contain Apply Filter, Cancel, and Clear Filter buttons
6. WHEN the Cancel button is clicked OR the overlay background is clicked THEN the overlay SHALL close
7. WHEN the Escape key is pressed THEN the overlay SHALL close

### Requirement 4: Date Range Validation

**User Story:** As a user, I want the system to validate my date inputs so that I don't submit invalid date ranges.

#### Acceptance Criteria

1. WHEN an end date is provided AND it is before the start date THEN an error message SHALL be displayed
2. WHEN date inputs are invalid THEN the Apply Filter button SHALL be disabled
3. WHEN date inputs become valid THEN the Apply Filter button SHALL be enabled
4. WHEN validation fails THEN clear error messages SHALL guide the user
5. WHEN the Apply Filter button is clicked THEN the date range SHALL be validated before processing

### Requirement 5: Date Filtering Functionality

**User Story:** As a user, I want to filter my transaction results by date range so that I can focus on transactions from specific time periods.

#### Acceptance Criteria

1. WHEN valid date range is applied THEN the system SHALL call the API with date parameters
2. WHEN only a Start Date is provided AND End Date is empty or null THEN the system SHALL show all transactions from that Start Date onward without applying an End Date filter in list format
3. WHEN only an End Date is provided THEN the system SHALL show all transactions up to that End Date in list format
4. WHEN both Start Date and End Date are provided AND 'List' view is selected THEN the system SHALL display filtered transactions as a list
5. WHEN both Start Date and End Date are provided AND 'Graph' view is selected THEN the system SHALL display filtered transactions as a Neo4j-style graph with nodes and relationships just like the main graph
6. WHEN Clear Filter is clicked THEN the original search results SHALL be restored

### Requirement 6: View Type Selection for Complete Date Ranges

**User Story:** As a user, I want to choose between List and Graph views when I provide both start and end dates so that I can visualize my filtered transactions in my preferred format.

#### Acceptance Criteria

1. WHEN both Start Date and End Date fields contain valid dates THEN the 'View Type' section SHALL become visible
2. WHEN the 'View Type' section is visible THEN it SHALL contain two radio button options: 'List' and 'Graph'
3. WHEN the modal opens THEN 'List' SHALL be selected as the default view type
4. WHEN a user selects 'Graph' option THEN the selection SHALL be visually indicated
5. WHEN a user selects 'List' option THEN the selection SHALL be visually indicated
6. WHEN either Start Date or End Date is cleared THEN the 'View Type' section SHALL be hidden

### Requirement 7: Error Handling and User Feedback

**User Story:** As a user, I want clear feedback about the filtering process so that I understand what's happening and can resolve any issues.

#### Acceptance Criteria

1. WHEN API calls fail THEN user-friendly error messages SHALL be displayed
2. WHEN no transactions match the date filter THEN a "No transactions found in selected date range" message SHALL be shown
3. WHEN date filtering is in progress THEN loading indicators SHALL be visible
4. WHEN a filter is successfully applied THEN the number of filtered transactions SHALL be displayed
5. WHEN operations complete successfully THEN previous error messages SHALL be cleared