/**
 * Google Apps Script for Hercules UK Contact Form & Newsletter
 * Version: 3.0.0 - English tab names and headers for UK site
 *
 * SETUP INSTRUCTIONS:
 * 1. Go to https://script.google.com
 * 2. Create a new project (or open existing)
 * 3. Replace the code with this script
 * 4. Update SPREADSHEET_ID with your Google Sheet ID
 * 5. Click Deploy > New deployment (or Manage deployments > Edit)
 * 6. Select "Web app"
 * 7. Set "Execute as" to "Me"
 * 8. Set "Who has access" to "Anyone"
 * 9. Click Deploy and copy the Web App URL
 * 10. Set GOOGLE_APPS_SCRIPT_URL secret in Cloudflare Worker
 *
 * MIGRATION FROM v2 (German): Run migrateToEnglish() once from the script
 * editor to rename existing German sheets/headers to English.
 */

// ============================================================================
// CONFIGURATION
// ============================================================================

// Your Google Spreadsheet ID (from the URL)
// Example: https://docs.google.com/spreadsheets/d/THIS_IS_YOUR_ID/edit
const SPREADSHEET_ID = '1s97QbIkSByUbOmA5fwiMxEPXjyxG9ofcZSv3mEMuJPo';

// Sheet names
const CONTACT_SHEET_NAME = 'Contact Enquiries';
const NEWSLETTER_SHEET_NAME = 'Newsletter';
const QUANTITY_REQUEST_SHEET_NAME = 'Quantity Requests';
const EXPRESS_DELIVERY_SHEET_NAME = 'Express Delivery';

// Old German sheet names (for migration)
const OLD_SHEET_NAMES = {
  'Kontaktanfragen': 'Contact Enquiries',
  'Mengenanfragen': 'Quantity Requests',
  'Expresslieferung': 'Express Delivery',
  // Newsletter stays the same
};

// Old German headers mapped to new English headers (for migration)
const OLD_HEADERS_MAP = {
  'Datum': 'Date',
  'Uhrzeit': 'Time',
  'Telefon': 'Phone',
  'Nachricht': 'Message',
  'Dateien': 'Files',
  'Seite': 'Page',
  'Zeitstempel': 'Timestamp',
  'Vorname': 'First Name',
  'Nachname': 'Last Name',
  'Produkt ID': 'Product ID',
  'Produktname': 'Product Name',
  'Menge': 'Quantity',
  'Stückpreis': 'Price Per Piece',
  'Seiten-URL': 'Page URL',
  'Quelle': 'Source',
  'Gewünschtes Lieferdatum': 'Desired Delivery Date',
};

// Column configurations for each sheet type
const SHEET_CONFIGS = {
  contact: {
    name: CONTACT_SHEET_NAME,
    headers: [
      'Date',
      'Time',
      'Name',
      'Email',
      'Phone',
      'Message',
      'Files',
      'Page',
      'URL',
      'Timestamp'
    ],
    filesColumnIndex: 7 // 1-based index
  },
  newsletter: {
    name: NEWSLETTER_SHEET_NAME,
    headers: [
      'Date',
      'Time',
      'Email',
      'Source',
      'Timestamp'
    ],
    filesColumnIndex: null // No files column
  },
  quantity_request: {
    name: QUANTITY_REQUEST_SHEET_NAME,
    headers: [
      'Date',
      'Time',
      'First Name',
      'Last Name',
      'Email',
      'Phone',
      'Product ID',
      'Product Name',
      'Quantity',
      'Price Per Piece',
      'Attributes',
      'Addons',
      'Message',
      'Files',
      'Page URL',
      'Timestamp'
    ],
    filesColumnIndex: 14
  },
  expressdelivery: {
    name: EXPRESS_DELIVERY_SHEET_NAME,
    headers: [
      'Date',
      'Time',
      'Name',
      'Email',
      'Phone',
      'Desired Delivery Date',
      'Product ID',
      'Product Name',
      'Quantity',
      'Price Per Piece',
      'Attributes',
      'Addons',
      'Message',
      'Files',
      'Page URL',
      'Timestamp'
    ],
    filesColumnIndex: 14
  }
};

// ============================================================================
// MAIN REQUEST HANDLERS
// ============================================================================

function doGet(e) {
  return handleRequest(e);
}

function doPost(e) {
  return handleRequest(e);
}

function handleRequest(e) {
  try {
    // Handle test calls without parameters
    if (!e || !e.parameter) {
      return ContentService
        .createTextOutput(JSON.stringify({
          success: false,
          error: 'No parameters provided. Use testScript() for testing.'
        }))
        .setMimeType(ContentService.MimeType.JSON);
    }

    const params = e.parameter;
    // Check both 'type' (newsletter) and 'formType' (contact/quantity)
    const type = params.type || params.formType || 'contact';

    const spreadsheet = SpreadsheetApp.openById(SPREADSHEET_ID);

    if (type === 'newsletter') {
      return handleNewsletter(spreadsheet, params);
    } else if (type === 'quantity_request' || type === 'quantity') {
      return handleQuantityRequest(spreadsheet, params);
    } else if (type === 'expressdelivery' || type === 'express_delivery') {
      return handleExpressDelivery(spreadsheet, params);
    } else {
      return handleContact(spreadsheet, params);
    }
  } catch (error) {
    console.error('Error handling request:', error);
    return ContentService
      .createTextOutput(JSON.stringify({ success: false, error: error.message }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

// ============================================================================
// SHEET HELPERS
// ============================================================================

/**
 * Get or create a sheet with the correct headers
 */
function getOrCreateSheet(spreadsheet, config) {
  let sheet = spreadsheet.getSheetByName(config.name);

  if (!sheet) {
    // Create new sheet with headers
    sheet = spreadsheet.insertSheet(config.name);
    sheet.appendRow(config.headers);
    sheet.getRange(1, 1, 1, config.headers.length).setFontWeight('bold');

    // Set column widths for better readability
    if (config.name === QUANTITY_REQUEST_SHEET_NAME || config.name === EXPRESS_DELIVERY_SHEET_NAME) {
      sheet.setColumnWidth(8, 200);  // Product Name wider
      sheet.setColumnWidth(11, 200); // Attributes column wider
      sheet.setColumnWidth(12, 200); // Addons column wider
      sheet.setColumnWidth(13, 300); // Message column wider
      sheet.setColumnWidth(14, 400); // Files column wider for URLs
    }
    if (config.name === CONTACT_SHEET_NAME) {
      sheet.setColumnWidth(6, 300);  // Message wider
      sheet.setColumnWidth(7, 400);  // Files wider for URLs
    }
  } else {
    // Check if "Files" column exists and add if missing
    ensureFilesColumn(sheet, config);
  }

  return sheet;
}

/**
 * Ensure the "Files" column exists in an existing sheet
 */
function ensureFilesColumn(sheet, config) {
  if (!config.filesColumnIndex) return; // Sheet doesn't need files column

  const headers = sheet.getRange(1, 1, 1, sheet.getLastColumn()).getValues()[0];
  const hasFilesColumn = headers.includes('Files') || headers.includes('Dateien');

  if (!hasFilesColumn) {
    // Insert "Files" column at the correct position
    const insertPosition = config.filesColumnIndex;
    sheet.insertColumnBefore(insertPosition);
    sheet.getRange(1, insertPosition).setValue('Files').setFontWeight('bold');
    sheet.setColumnWidth(insertPosition, 400); // Wide enough for URLs

    console.log(`Added "Files" column to ${config.name} at position ${insertPosition}`);
  }
}

// ============================================================================
// FORM HANDLERS
// ============================================================================

function handleContact(spreadsheet, params) {
  const config = SHEET_CONFIGS.contact;
  const sheet = getOrCreateSheet(spreadsheet, config);

  // Add the contact data
  sheet.appendRow([
    params.date || new Date().toLocaleDateString('en-GB'),
    params.time || new Date().toLocaleTimeString('en-GB'),
    params.name || '',
    params.email || '',
    params.phone || '',
    params.message || '',
    params.files || '',  // File URLs from R2
    params.pageTitle || '',
    params.pageUrl || '',
    new Date().toISOString()
  ]);

  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: 'Data saved to Contact Enquiries' }))
    .setMimeType(ContentService.MimeType.JSON);
}

function handleNewsletter(spreadsheet, params) {
  const config = SHEET_CONFIGS.newsletter;
  const sheet = getOrCreateSheet(spreadsheet, config);

  // Check for duplicate email
  const data = sheet.getDataRange().getValues();
  const emails = data.map(row => row[2]); // Email is in column 3 (index 2)

  if (emails.includes(params.email)) {
    return ContentService
      .createTextOutput(JSON.stringify({ success: true, message: 'Email already subscribed' }))
      .setMimeType(ContentService.MimeType.JSON);
  }

  // Add the newsletter subscription
  sheet.appendRow([
    params.date || new Date().toLocaleDateString('en-GB'),
    params.time || new Date().toLocaleTimeString('en-GB'),
    params.email || '',
    params.source || '',
    new Date().toISOString()
  ]);

  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: 'Data saved to Newsletter' }))
    .setMimeType(ContentService.MimeType.JSON);
}

function handleQuantityRequest(spreadsheet, params) {
  const config = SHEET_CONFIGS.quantity_request;
  const sheet = getOrCreateSheet(spreadsheet, config);

  // Parse name into first/last if provided as single field
  let firstName = params.firstName || '';
  let lastName = params.lastName || '';
  if (!firstName && !lastName && params.name) {
    const nameParts = params.name.split(' ');
    firstName = nameParts[0] || '';
    lastName = nameParts.slice(1).join(' ') || '';
  }

  // Add the quantity request data
  sheet.appendRow([
    params.date || new Date().toLocaleDateString('en-GB'),
    params.time || new Date().toLocaleTimeString('en-GB'),
    firstName,
    lastName,
    params.email || '',
    params.phone || '',
    params.productId || '',
    params.productName || '',
    params.quantity || '',
    params.pricePerPiece || '',
    params.attributes || '',
    params.addons || '',
    params.message || '',
    params.files || '',  // File URLs from R2
    params.pageUrl || '',
    new Date().toISOString()
  ]);

  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: 'Data saved to Quantity Requests' }))
    .setMimeType(ContentService.MimeType.JSON);
}

function handleExpressDelivery(spreadsheet, params) {
  const config = SHEET_CONFIGS.expressdelivery;
  const sheet = getOrCreateSheet(spreadsheet, config);

  // Add the express delivery request data
  sheet.appendRow([
    params.date || new Date().toLocaleDateString('en-GB'),
    params.time || new Date().toLocaleTimeString('en-GB'),
    params.name || '',
    params.email || '',
    params.phone || '',
    params.desiredDate || '',
    params.productId || '',
    params.productName || '',
    params.quantity || '',
    params.pricePerPiece || '',
    params.attributes || '',
    params.addons || '',
    params.message || '',
    params.files || '',  // File URLs from R2
    params.pageUrl || '',
    new Date().toISOString()
  ]);

  return ContentService
    .createTextOutput(JSON.stringify({ success: true, message: 'Data saved to Express Delivery' }))
    .setMimeType(ContentService.MimeType.JSON);
}

// ============================================================================
// MIGRATION: German → English (Run once from script editor)
// ============================================================================

/**
 * Migrate existing German sheet names and headers to English.
 * Run this once from: Run > Run function > migrateToEnglish
 *
 * This will:
 * 1. Rename tabs: Kontaktanfragen → Contact Enquiries, etc.
 * 2. Rename column headers: Datum → Date, Telefon → Phone, etc.
 * 3. Preserve all existing data rows
 */
function migrateToEnglish() {
  const spreadsheet = SpreadsheetApp.openById(SPREADSHEET_ID);

  Logger.clear();
  Logger.log('=== Migrating German sheets to English ===');
  Logger.log('');

  // Step 1: Rename sheet tabs
  for (const [oldName, newName] of Object.entries(OLD_SHEET_NAMES)) {
    const sheet = spreadsheet.getSheetByName(oldName);
    if (sheet) {
      sheet.setName(newName);
      Logger.log('Renamed tab: "' + oldName + '" → "' + newName + '"');
    } else {
      Logger.log('Tab "' + oldName + '" not found (skipped)');
    }
  }
  Logger.log('');

  // Step 2: Rename column headers on each sheet
  const allSheets = spreadsheet.getSheets();
  for (const sheet of allSheets) {
    const sheetName = sheet.getName();
    const lastCol = sheet.getLastColumn();
    if (lastCol === 0) continue;

    const headerRange = sheet.getRange(1, 1, 1, lastCol);
    const headers = headerRange.getValues()[0];
    let changed = false;

    for (let i = 0; i < headers.length; i++) {
      const oldHeader = headers[i];
      if (OLD_HEADERS_MAP[oldHeader]) {
        headers[i] = OLD_HEADERS_MAP[oldHeader];
        changed = true;
      }
    }

    if (changed) {
      headerRange.setValues([headers]);
      Logger.log('Updated headers in "' + sheetName + '": ' + headers.join(', '));
    } else {
      Logger.log('No header changes needed in "' + sheetName + '"');
    }
  }

  Logger.log('');
  Logger.log('=== Migration complete! ===');
  Logger.log('');
  Logger.log('Next: Deploy > Manage deployments > Edit > New version > Deploy');

  return 'Migration complete. Check the execution log for details.';
}

// ============================================================================
// LEGACY MIGRATION - Add "Files" column to sheets missing it
// ============================================================================

/**
 * Run this function manually from the script editor to add Files column.
 * Go to: Run > Run function > migrateAllSheets
 */
function migrateAllSheets() {
  const spreadsheet = SpreadsheetApp.openById(SPREADSHEET_ID);

  console.log('Starting migration...');

  // Migrate each sheet type
  for (const [type, config] of Object.entries(SHEET_CONFIGS)) {
    if (!config.filesColumnIndex) {
      console.log(`Skipping ${config.name} - no files column needed`);
      continue;
    }

    const sheet = spreadsheet.getSheetByName(config.name);
    if (sheet) {
      console.log(`Checking ${config.name}...`);
      ensureFilesColumn(sheet, config);
    } else {
      console.log(`Sheet ${config.name} does not exist yet - will be created on first submission`);
    }
  }

  console.log('Migration complete!');

  return 'Migration complete. Check the execution log for details.';
}

/**
 * Test function to verify the script is working
 * Run this from: Run > Run function > testScript
 */
function testScript() {
  try {
    Logger.clear();
    Logger.log('=== Testing Hercules UK Form Handler ===');
    Logger.log('');

    // Test 1: Spreadsheet Connection
    Logger.log('Test 1: Connecting to spreadsheet...');
    const spreadsheet = SpreadsheetApp.openById(SPREADSHEET_ID);
    Logger.log('✓ Successfully connected to: ' + spreadsheet.getName());
    Logger.log('✓ Spreadsheet ID: ' + SPREADSHEET_ID);
    Logger.log('');

    // Test 2: List Existing Sheets
    Logger.log('Test 2: Checking existing sheets...');
    const sheets = spreadsheet.getSheets().map(s => s.getName());
    Logger.log('✓ Found ' + sheets.length + ' sheet(s): ' + sheets.join(', '));
    Logger.log('');

    // Test 3: Expected Sheets
    Logger.log('Test 3: Expected sheets:');
    Logger.log('  - ' + CONTACT_SHEET_NAME);
    Logger.log('  - ' + NEWSLETTER_SHEET_NAME);
    Logger.log('  - ' + QUANTITY_REQUEST_SHEET_NAME);
    Logger.log('  - ' + EXPRESS_DELIVERY_SHEET_NAME);
    Logger.log('  (These will be created automatically on first form submission)');
    Logger.log('');

    Logger.log('=== All Tests Passed! ✓ ===');
    Logger.log('');
    Logger.log('Next step: Deploy > New deployment > Web app');

    return 'SUCCESS: Script is configured correctly!';
  } catch (error) {
    Logger.log('=== ERROR ===');
    Logger.log('✗ ' + error.toString());
    Logger.log('');
    Logger.log('Make sure:');
    Logger.log('1. Spreadsheet ID is correct: ' + SPREADSHEET_ID);
    Logger.log('2. You have edit access to this spreadsheet');
    Logger.log('3. You granted permissions when prompted');

    return 'ERROR: ' + error.toString();
  }
}
