# Design Document

## Overview

The team photo management system needs to integrate the existing PNG photo files (Gursimran.png, Manav.png, Vatsal.png) with the current HTML structure that expects JPG files. The system currently has fallback "Photo" placeholders that display when the expected JPG files are not found.

## Architecture

The solution involves updating the HTML image source references to point to the correct PNG files and ensuring proper fallback handling. The system has two photo display contexts:

1. **Team Member Cards**: Static display in the main page showing team member information
2. **Help Modal Upload System**: Interactive photo upload interface for customization

## Components and Interfaces

### 1. Static Team Photo Display

**Current Implementation:**
- HTML `<img>` tags with `src` attributes pointing to `.jpg` files
- `onerror` handlers that show fallback "Photo" placeholders
- Fixed dimensions: 120px width × 150px height

**Required Changes:**
- Update `src` attributes from `.jpg` to `.png` extensions
- Maintain existing fallback mechanism
- Preserve styling and dimensions

### 2. Photo File Management

**Current State:**
- Expected files: `gursimran.jpg`, `manav.jpg`, `vatsal.jpg`
- Actual files: `Gursimran.png`, `Manav.png`, `Vatsal.png`

**File Mapping Strategy:**
- Update HTML references to match actual filenames
- Consider case sensitivity (capital vs lowercase)
- Maintain consistent naming convention

### 3. Fallback System

**Current Mechanism:**
```html
<img src="filename.jpg" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
<div class="member-photo" style="display: none;">Photo</div>
```

**Enhanced Fallback:**
- Preserve existing error handling
- Add loading states if needed
- Ensure graceful degradation

## Data Models

### Team Member Structure
```javascript
{
  name: string,           // "Gursimranpreet kaur"
  filename: string,       // "Gursimran.png"
  altText: string,        // "Gursimranpreet kaur"
  position: string,       // "(Desh Bhagat University)"
  role: string           // "NCFL Intern"
}
```

### Photo Configuration
```javascript
{
  width: "120px",
  height: "150px",
  objectFit: "cover",
  borderRadius: "8px",
  fallbackDisplay: "flex"
}
```

## Error Handling

### File Not Found Scenarios
1. **Primary Image Missing**: Display fallback "Photo" placeholder
2. **Network Issues**: Graceful degradation to placeholder
3. **Invalid File Format**: Error logging and fallback display

### Implementation Strategy
1. Update HTML `src` attributes to correct filenames
2. Test image loading with browser developer tools
3. Verify fallback mechanism works correctly
4. Ensure consistent styling across all team members

## Testing Strategy

### Manual Testing
1. **File Existence Verification**
   - Confirm all PNG files exist in root directory
   - Verify correct capitalization and extensions

2. **Browser Loading Test**
   - Load page and verify images display correctly
   - Test fallback by temporarily renaming image files
   - Check responsive behavior on different screen sizes

3. **Cross-Browser Compatibility**
   - Test in Chrome, Firefox, Safari, Edge
   - Verify image loading and fallback behavior

### Automated Testing Considerations
- File existence checks could be added to build process
- Image loading validation through JavaScript
- Fallback mechanism testing through DOM manipulation

## Implementation Plan

### Phase 1: Direct File Reference Update
- Update HTML `src` attributes from `.jpg` to `.png`
- Match exact filenames including capitalization
- Test immediate functionality

### Phase 2: Enhanced Error Handling
- Add console logging for debugging
- Implement loading states if needed
- Optimize image loading performance

### Phase 3: Future Extensibility
- Create configuration object for easy team member updates
- Add support for different image formats
- Implement dynamic team member loading

## Security Considerations

- Ensure image files are served from trusted sources
- Validate file types and sizes if implementing upload functionality
- Prevent XSS through proper image handling

## Performance Considerations

- Optimize image file sizes for web delivery
- Consider lazy loading for better page performance
- Implement proper caching headers for static images