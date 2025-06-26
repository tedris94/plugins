# Educare Custom Template - Mobile Optimized

## Features
- ✅ A4 Single Page Print Layout
- ✅ Background Color Retention in Print
- ✅ Mobile Responsive Design
- ✅ Touch-Friendly Interface
- ✅ Horizontal Scroll for Grade Sheet on Mobile
- ✅ **Key to Grading Table** - Configurable grading system display

## Mobile Optimization

### Viewport Meta Tag
Ensure your WordPress theme includes this meta tag in the `<head>` section:
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

### Responsive Breakpoints
- **Desktop**: > 768px - Full layout
- **Tablet**: 768px - Optimized layout with horizontal scroll
- **Mobile**: 480px - Compact layout with touch-friendly buttons
- **Landscape**: Special handling for landscape orientation

### Print Optimization
- **Page Size**: A4 (210mm × 297mm)
- **Margins**: 8mm all around
- **Font Sizes**: Optimized for single page (7pt - 12pt)
- **Background Colors**: Retained using print-color-adjust
- **Content Fitting**: Max height 277mm to ensure single page

### Mobile Features
- Horizontal scrollable grade sheet with scroll indicator
- Touch-friendly buttons (44px minimum)
- Compact layouts for small screens
- Optimized font sizes for readability
- Reduced motion support for accessibility

## Usage
1. Activate the plugin
2. Go to Educare → Settings → Card Settings
3. Select "BOS Report Card Template"
4. Configure your fields
5. **Enable "Key to Grading"** to show grading system table
6. Test on mobile devices and print preview

## Key to Grading Feature
- **Location**: Appears after PSYCHOMOTOR ASSESSMENT section
- **Content**: Displays the grading system from Educare → Settings → Manage Grading System
- **Configurable**: Can be enabled/disabled from Card Settings
- **Columns**: MARKS | GRADE | POINT | REMARK
- **Responsive**: Optimized for mobile and print

## Browser Support
- Chrome/Safari: Full support including print colors
- Firefox: Full support
- Edge: Full support
- Mobile browsers: Optimized touch experience 