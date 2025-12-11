# PROJECT UPDATES - Smart Presentation Hokkaido Showroom

## Executive Summary

This document provides a comprehensive report on the transformation of the **Smart Presentation Hokkaido Showroom** application from a static file-based system (JSON) to a dynamic Content Management System (CMS) with MySQL database. This update includes implementation of authentication system, CRUD modules for content management, and enhanced data management interface for administrators.

---

## System Comparison: Before and After

### Before Implementation
- No login system - all visitors accessed as regular users
- Product data stored in data/data.json (hardcoded)
- Catalog PDF data stored in PHP arrays in catalog-page.php
- Video data stored in PHP arrays in company-playlist.php
- No interface for editing/adding/deleting content
- Administrators required source code editing for data updates
- High error risk during manual JSON/array editing
- No user tracking or audit logging

### After Implementation
- Login system with Admin and User roles
- Structured MySQL database (6 relational tables)
- Administrators can perform CRUD operations on Products, Catalogs, Videos, and Playlists
- User-friendly modal form interface for content management
- File upload with validation (PDF, MP4, MP3, GLB, JPG/PNG)
- Real-time updates without source code modification
- Rich text editor (TinyMCE) for product descriptions
- Session management with 30-minute timeout
- Interactive 3D annotation system (under development)

---

## New Features and Updates

### 1. Authentication and Security System

#### Login Page Implementation
**Files:** `pages/login.php`, `auth/login_process.php`

**Features:**
- Login form with username and password fields
- Session management using PHP native sessions
- Automatic redirect to home page after successful authentication
- Logout functionality with session destruction (`auth/logout.php`)

#### Role-Based Access Control (RBAC)
**Two Primary Roles:**
- **Admin:** Full access (view + CRUD operations)
- **User:** View-only access (cannot edit or delete)

**Implementation:**
  ```php
  // middleware/auth_check.php - Check if user is logged in
  if (!isset($_SESSION['user_id'])) {
      header('Location: /login');
      exit;
  }
  
  // middleware/admin_check.php - Check if user is admin
  if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
      header('Location: /home');
      exit;
  }
  ```

#### Session Management
**Configuration:** `config.php`
- Session timeout: 30 minutes (1800 seconds)
- HTTPOnly cookies: XSS protection
- SameSite: Strict (CSRF protection)
- Session regeneration on each login for security

#### Default Credentials
```
Username: admin
Password: admin123
```
Note: Passwords are hashed using `password_hash()` with bcrypt algorithm.

---

### 2. Database Architecture and Data Migration

#### Database Schema
**Database Name:** `hokkaido_showroom_db`

**Table Structure:**

```sql
1. users
   - id (PK, AUTO_INCREMENT)
   - username (UNIQUE)
   - password_hash
   - role (ENUM: 'admin', 'user')
   - created_at

2. products
   - id (PK, AUTO_INCREMENT)
   - product_key (UNIQUE)
   - category (ENUM: 'crushing', 'recycling')
   - display_title_jp
   - display_title_en
   - preview_img_path
   - info (TEXT for HTML description)
   - glb_file (path to 3D model)
   - audio_link, pdf_link, video_link
   - info_img
   - camera_pos_x, camera_pos_y, camera_pos_z
   - created_at, updated_at

3. annotations
   - id (PK, AUTO_INCREMENT)
   - product_id (FK to products)
   - text
   - pos_x, pos_y, pos_z (DECIMAL for 3D coordinates)
   - created_at

4. catalogs
   - id (PK, AUTO_INCREMENT)
   - title
   - pdf_file
   - preview_image
   - sort_order (for display ordering)
   - created_at, updated_at

5. video_playlists
   - id (PK, AUTO_INCREMENT)
   - title
   - thumbnail_image
   - sort_order
   - created_at, updated_at

6. videos
   - id (PK, AUTO_INCREMENT)
   - playlist_id (FK to video_playlists)
   - file_name
   - title
   - duration (VARCHAR format HH:MM:SS)
   - sort_order
   - created_at, updated_at
```

**Entity Relationships:**
```
users (1) ────── (∞) [audit logs - future implementation]
products (1) ────── (∞) annotations
video_playlists (1) ────── (∞) videos
```

#### Automated Data Migration
**File:** `migrate_data.php`

**Functions:**
1. Read `data/data.json` (legacy product data)
2. Read PHP arrays from `catalog-page.php` and `company-playlist.php`
3. Transform and insert into MySQL database
4. Automatic mapping of categories, preview images, and table relationships

**Execution Process:**
```
1. Import database/schema.sql to phpMyAdmin
2. Access via browser: http://localhost/[project]/migrate_data.php
3. Data automatically migrated
```

**Migration Output:**
```
- 1 Admin user created
- 13 Products migrated
- 13 Annotations migrated
- 12 Catalog PDFs migrated
- 12 Video playlists migrated
- 100+ Videos migrated
```

#### Database Connection
**File:** `config.php`
**Technology:** PDO (PHP Data Objects)

**Advantages:**
- Prepared statements (SQL injection protection)
- Multiple database type support
- Exception-based error handling
  ```php
  $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  ```

---

### 3. Catalog Management Module (Catalog CMS)

#### Transformation: Static to Dynamic

**Before:**
```php
// Hardcoded array in catalog-page.php
$pdfList = [
    "会社案内" => "会社案内.pdf",
    "総合カタログ" => "総合カタログ.pdf",
    // ... code editing required for updates
];
```

**After:**
```php
// Database-driven data with API CRUD operations
api/catalog_api.php?action=get_all
```

#### API Endpoints

**File:** `api/catalog_api.php`

| Endpoint | Method | Auth | Description |
|----------|--------|------|-----------|
| `?action=get_all` | GET | Public | Get all catalogs |
| `?action=get_one&id=1` | GET | Public | Get catalog details |
| `?action=add` | POST | Admin | Add new catalog |
| `?action=update` | POST | Admin | Update catalog |
| `?action=delete` | POST | Admin | Delete catalog |

#### File Upload Features
- **PDF Upload:** Maximum 10MB with MIME type validation
- **Preview Image Upload:** JPG/PNG format with automatic resizing if needed
- **Storage Path:** `files/pdf/` and `files/playlists/`

#### Admin Interface
**Location:** Modal in `pages/catalog-page.php`

**Features:**
- "Add Catalog" button (Admin only)
- "Edit" and "Delete" buttons per item
- Upload form with preview
- Sort order for display arrangement
- Toast notifications for operation feedback (success/error)

#### JavaScript Handler
**File:** `js/catalog-page.js`

**Functions:**
- `loadCatalogs()` - Fetch data via AJAX
- `openAddModal()` - Open add form
- `openEditModal(id)` - Open edit form with pre-filled data
- `deleteCatalog(id)` - Confirmation and deletion via API
- File upload using FormData
- Error handling and loading states

---

### 4. Video and Playlist Management Module (Video CMS)

#### Nested Data Structure

Playlist → Videos (One-to-Many Relationship)

**Before:**
```php
// Nested array in company-playlist.php
$playlists = [
    "1.jpeg" => [
        "title" => "インフォメーション",
        "videos" => [ /* video array */ ]
    ]
];
```

**After:**
- Relational database with Foreign Key constraints
- API supporting nested operations

#### API Endpoints

**File:** `api/video_api.php`

**Playlist Management:**
| Endpoint | Method | Auth | Description |
|----------|--------|------|-----------|
| `?action=get_playlists` | GET | Public | Get all playlists |
| `?action=get_playlist&id=1` | GET | Public | Get playlist details |
| `?action=add_playlist` | POST | Admin | Add playlist |
| `?action=update_playlist` | POST | Admin | Update playlist |
| `?action=delete_playlist` | POST | Admin | Delete playlist (cascade) |

**Video Management:**
| Endpoint | Method | Auth | Description |
|----------|--------|------|-----------|
| `?action=get_videos&playlist_id=1` | GET | Public | Retrieve videos by playlist |
| `?action=add_video` | POST | Admin | Add video to playlist |
| `?action=update_video` | POST | Admin | Update video |
| `?action=delete_video` | POST | Admin | Delete video |

#### Auto-Duration Detection Feature

**Process Flow:**
1. User uploads MP4 video file
2. JavaScript reads file using HTML5 Video API
3. Extract `video.duration` in seconds
4. Convert to `HH:MM:SS` format
5. Auto-fill "Duration" input field

**Implementation:**
```javascript
// js/company-playlist.js
videoFileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    const video = document.createElement('video');
    video.preload = 'metadata';
    
    video.onloadedmetadata = function() {
        const duration = formatDuration(video.duration);
        durationInput.value = duration; // Auto-fill
    };
    
    video.src = URL.createObjectURL(file);
});
```

#### Admin Interface
**Two-Level Modal Structure:**
1. **Playlist Modal** - Manage playlist (title, thumbnail, sort order)
2. **Video Modal** - Manage videos within playlist (nested)

**Features:**
- "Manage Videos" button to access video level
- Breadcrumb navigation (Playlist → Videos)
- Drag-and-drop sorting (planned enhancement)

#### JavaScript Handler
**File:** `js/company-playlist.js`

**Functions:**
- `loadPlaylists()` - Load all playlists
- `openPlaylistModal()` - Add/edit playlist
- `openVideoModal(playlistId)` - Manage videos per playlist
- `autoDetectDuration()` - Auto-fill video duration
- Nested AJAX calls for relational data

---

### 5. Dynamic 3D Showroom Product Module

#### Transformation: JSON to Database

**Before:**
```javascript
// diorama.js
fetch('data/data.json')
    .then(response => response.json())
    .then(data => {
        // Hardcoded JSON file
    });
```

**After:**
```php
// crushing-plant.php / recycling-plant.php
<script>
    window.PRODUCT_DATA = <?php echo json_encode($products); ?>;
</script>
```
```javascript
// diorama.js
const products = window.PRODUCT_DATA; // Data from database
```

#### API Endpoints

**File:** `api/product_api.php`

| Endpoint | Method | Auth | Description |
|----------|--------|------|-----------|
| `?action=get_all&category=crushing` | GET | Public | Retrieve products by category |
| `?action=get_one&id=1` | GET | Public | Get single product details |
| `?action=add` | POST | Admin | Add new product |
| `?action=update` | POST | Admin | Update product |
| `?action=delete` | POST | Admin | Delete product |

#### Rich Text Editor (TinyMCE)

**Implementation:**
```html
<!-- pages/crushing-plant.php -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script>
    tinymce.init({
        selector: '#product_info',
        height: 300,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'bold italic | bullist numlist | link'
    });
</script>
```

**TinyMCE Integration:**
- Product Description (`info` field)
- Output: Sanitized HTML for safe display

**Capabilities:**
- Text formatting (bold, italic, bullet points)
- Real-time preview
- Clean HTML output

#### Selective Asset Update

Administrators can update specific assets without re-uploading all files.

**Example Scenario:**
```
Product "NE750J" has:
- 3D Model (GLB): Available
- Audio: Available
- PDF: Not available
- Video: Not available

Administrator can:
- Update only PDF without re-uploading GLB/Audio
- System logic: "If new file uploaded, replace. If not, keep existing."
```

**Implementation:**
```php
// api/product_api.php - Update logic
if (isset($_FILES['glb_file']) && $_FILES['glb_file']['error'] === UPLOAD_ERR_OK) {
    $glb_file = uploadFile($_FILES['glb_file'], 'glb');
} else {
    $glb_file = $_POST['existing_glb'] ?? null; // Keep existing
}
```

#### 3D Rendering Integration
**Library:** Three.js
**File:** `js/diorama.js`

**Functions:**
- Load GLB models from database paths
- Set camera positions from database coordinates
- Render annotations from `annotations` table
- Interactive hover and click events

---

### 6. Interactive 3D Annotation System

**Status:** Under Development

#### Implemented Features

**1. Database Structure**
- `annotations` table with 3D coordinates (pos_x, pos_y, pos_z) -> on development to change the structure into two points (target and label points)
- Foreign Key relationship to `products` table

**2. Click-to-Place Annotation**
**Technology:** Three.js Raycasting

**Process:**
  ```javascript
  // js/annotation-admin.js
  canvas.addEventListener('click', function(event) {
      // 1. Convert mouse position to 3D coordinates
      raycaster.setFromCamera(mouse, camera);
      
      // 2. Detect intersection with 3D model
      const intersects = raycaster.intersectObject(model);
      
      // 3. Get coordinates of clicked point
      if (intersects.length > 0) {
          const point = intersects[0].point;
          // point.x, point.y, point.z = New annotation coordinates
      }
  });
  ```

**3. Annotation Management**
**API:** `api/product_api.php?action=add_annotation`

**Features:**
- View annotation list per product
- Delete annotations
- Edit annotation text

**4. Visual Feedback**
- 3D markers at annotation positions
- Hover effects for highlighting
- Label text always facing camera (CSS2DRenderer)

#### Features Under Development

**1. Drag-and-Drop Editing**
- Drag annotation markers to reposition
- Real-time coordinate updates
- Position saving via AJAX

**2. Testing and Refinement**
- Raycasting accuracy testing at various camera angles
- Z-index (depth) coordinate validation
- Placement flow UX improvements


#### Admin Interface
**Location:** `pages/crushing-plant.php`, `pages/recycling-plant.php`
**Controls:** "Manage Annotations" button per product card
**Modal:** Add/edit annotation form with coordinate preview

---

## Database Structure (Entity Relationship Diagram)

![Database ERD](ERD.png)

---

## API Documentation

### Authentication API

#### Login
```http
POST /auth/login_process.php
Content-Type: application/x-www-form-urlencoded

username=admin&password=admin123
```

**Response (Success):**
```php
// Redirect to /home with session:
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
$_SESSION['role'] = 'admin';
```

**Response (Failed):**
```php
// Redirect to /login?error=invalid_credentials
```

#### Logout
```http
GET /auth/logout.php
```

**Response:**
```php
// Session destroyed, redirect to /login
session_destroy();
```

---

### Catalog API

#### Get All Catalogs
```http
GET /api/catalog_api.php?action=get_all
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "会社案内",
      "pdf_file": "会社案内.pdf",
      "preview_image": "会社案内.jpg",
      "sort_order": 10
    }
  ]
}
```

#### Add Catalog
```http
POST /api/catalog_api.php?action=add
Content-Type: multipart/form-data

title=新カタログ
pdf_file=[FILE]
preview_image=[FILE]
sort_order=20
```

**Response:**
```json
{
  "status": "success",
  "message": "Catalog added successfully",
  "id": 13
}
```

#### Update Catalog
```http
POST /api/catalog_api.php?action=update
Content-Type: multipart/form-data

id=1
title=Updated Title
pdf_file=[FILE] (optional)
preview_image=[FILE] (optional)
sort_order=10
```

#### Delete Catalog
```http
POST /api/catalog_api.php?action=delete
Content-Type: application/json

{
  "id": 1
}
```

---

### Video API

#### Get All Playlists
```http
GET /api/video_api.php?action=get_playlists
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "title": "インフォメーション",
      "thumbnail_image": "1.jpeg",
      "sort_order": 10,
      "video_count": 4
    }
  ]
}
```

#### Get Videos by Playlist
```http
GET /api/video_api.php?action=get_videos&playlist_id=1
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "playlist_id": 1,
      "file_name": "砕石フォーラム2024.mp4",
      "title": "砕石フォーラム2024",
      "duration": "00:00:53",
      "sort_order": 10
    }
  ]
}
```

#### Add Video to Playlist
```http
POST /api/video_api.php?action=add_video
Content-Type: multipart/form-data

playlist_id=1
video_file=[FILE]
title=New Video
duration=00:02:30 (auto-detected)
sort_order=20
```

---

### Product API

#### Get Products by Category
```http
GET /api/product_api.php?action=get_all&category=crushing
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "product_key": "Hokkaido Crushing Full Plant",
      "category": "crushing",
      "display_title_jp": "破砕プラント (Overview)",
      "display_title_en": "Hokkaido Crushing Full Plant",
      "preview_img_path": "files/img_preview/...",
      "info": "<p>Rich HTML description</p>",
      "glb_file": "files/glb/model.glb",
      "audio_link": "audio/audio.mp3",
      "camera_pos_x": 0,
      "camera_pos_y": 5,
      "camera_pos_z": 10
    }
  ]
}
```

#### Update Product
```http
POST /api/product_api.php?action=update
Content-Type: multipart/form-data

id=1
display_title_jp=Updated Title
info=<p>New HTML description</p>
glb_file=[FILE] (optional)
audio_link=[FILE] (optional)
pdf_link=[FILE] (optional)
```

---

## Technical Implementation Details

### Session Management

**File:** `config.php`

```php
// Session configuration
ini_set('session.gc_maxlifetime', 1800); // 30 minutes
session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => '',
    'secure' => false,  // Set true for HTTPS
    'httponly' => true, // XSS protection
    'samesite' => 'Strict' // CSRF protection
]);
session_start();
```

**Session Regeneration:**
```php
// auth/login_process.php - On each new login
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
```

---

### File Upload Security

**Files:** `api/catalog_api.php`, `api/video_api.php`, `api/product_api.php`

**MIME Type Validation:**
```php
function validateFileType($file, $allowedTypes) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    return in_array($mimeType, $allowedTypes);
}

// Allowed types
$allowedPDF = ['application/pdf'];
$allowedVideo = ['video/mp4'];
$allowedImage = ['image/jpeg', 'image/png'];
$allowedAudio = ['audio/mpeg', 'audio/mp3'];
$allowedGLB = ['model/gltf-binary', 'application/octet-stream'];
```

**File Size Limit:**
```php
// php.ini or .htaccess
upload_max_filesize = 100M
post_max_size = 100M
```

**Unique Filename Generation:**
```php
function generateUniqueFilename($originalName) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}
```

---

### XSS Protection

**Output Escaping:**
```php
// For HTML output
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');

// For HTML attributes
echo htmlspecialchars($userInput, ENT_QUOTES | ENT_HTML5, 'UTF-8');
```

**TinyMCE Sanitization:**
```javascript
tinymce.init({
    selector: '#product_info',
    valid_elements: 'p,strong,em,ul,ol,li,a[href|target]', // Whitelist tags
    invalid_elements: 'script,iframe', // Blacklist dangerous tags
});
```

---

### SQL Injection Protection

**Prepared Statements:**
```php
// SECURE - Using PDO prepared statements
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);

// INSECURE - Direct concatenation (VULNERABLE)
// $query = "SELECT * FROM products WHERE id = " . $_GET['id'];
```

---

### AJAX Error Handling

**JavaScript Pattern:**
```javascript
// js/catalog-page.js
function loadCatalogs() {
    fetch('/api/catalog_api.php?action=get_all')
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.json();
        })
        .then(data => {
            if (data.status === 'error') {
                showToast(data.message, 'error');
                return;
            }
            renderCatalogs(data.data);
        })
        .catch(error => {
            showToast('Failed to load catalogs: ' + error.message, 'error');
            console.error('Error:', error);
        });
}
```

---

### 3D Raycasting Implementation

**File:** `js/annotation-admin.js`

```javascript
// Setup raycaster
const raycaster = new THREE.Raycaster();
const mouse = new THREE.Vector2();

// Click event handler
canvas.addEventListener('click', function(event) {
    // 1. Normalize mouse coordinates (-1 to +1)
    const rect = canvas.getBoundingClientRect();
    mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
    mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
    
    // 2. Set raycaster from camera
    raycaster.setFromCamera(mouse, camera);
    
    // 3. Calculate intersections
    const intersects = raycaster.intersectObject(modelMesh, true);
    
    if (intersects.length > 0) {
        const point = intersects[0].point;
        
        // 4. Create annotation at clicked position
        createAnnotation(point.x, point.y, point.z);
    }
});

function createAnnotation(x, y, z) {
    // Create visual marker
    const geometry = new THREE.SphereGeometry(0.1, 16, 16);
    const material = new THREE.MeshBasicMaterial({ color: 0xff0000 });
    const marker = new THREE.Mesh(geometry, material);
    marker.position.set(x, y, z);
    scene.add(marker);
    
    // Save to database via AJAX
    saveAnnotationToDatabase(x, y, z);
}
```

---

## Installation and Setup

For detailed installation instructions, system requirements, and troubleshooting guide, please refer to [docs/README.md](README.md).

Quick start:
1. Import `database/schema.sql` to phpMyAdmin
2. Configure `config.php` with database credentials
3. Access: `http://localhost/Smart_Presentation_Hokkaido_Showroom`
4. Login: `admin` / `admin123`

---

## File Structure Changes

### New Directories
```
auth/                    # Authentication handlers
  ├── login_process.php  # Login logic
  └── logout.php         # Logout logic

middleware/              # Middleware checks
  ├── auth_check.php     # Session validation
  └── admin_check.php    # Admin role check

api/                     # REST API endpoints
  ├── catalog_api.php    # Catalog CRUD
  ├── video_api.php      # Video & Playlist CRUD
  └── product_api.php    # Product CRUD

database/                # Database files
  └── schema.sql         # Database export

.env.example             # Environment template
```

### Modified Files
```
config.php               # Database connection, session configuration
index.php                # Routing with middleware

pages/
  ├── login.php          # New login page
  ├── home.php           # Logout link, role-based UI
  ├── catalog-page.php   # Dynamic data + Admin CRUD UI
  ├── company-playlist.php # Dynamic data + Admin CRUD UI
  ├── company-video.php  # Dynamic video player
  ├── crushing-plant.php # Dynamic products + Annotation UI
  └── recycling-plant.php # Dynamic products + Annotation UI

js/
  ├── catalog-page.js    # AJAX CRUD operations
  ├── company-playlist.js # Nested CRUD + auto-duration
  ├── product-admin.js   # Product CRUD + file uploads
  ├── annotation-admin.js # Raycasting + annotation management
  └── diorama.js         # Refactored for database integration

style/
  ├── login.css          # Login page styles
  └── admin-ui.css       # Admin interface styles
```

### Deprecated Files (Optional Cleanup)
```
data/data.json           # Still present for fallback, can be removed after migration
```

---

## Testing Notes

### Tested Features
- Login/Logout flow with session management
- CRUD Catalogs (Add, Edit, Delete, File Upload)
- CRUD Video Playlists (Add, Edit, Delete)
- CRUD Videos (Add to playlist, Edit, Delete, Auto-duration)
- CRUD Products (Add, Edit, Selective file update)
- 3D Model loading from database path
- Rich text editor (TinyMCE) for product descriptions
- Role-based access (Admin vs User)
- File upload validation (MIME type, size limit)
- Responsive UI for modal forms

### Features Under Testing
- Annotation click-to-place (accuracy at various zoom levels)
- Annotation drag-to-reposition (under development)
- Z-depth coordinates for annotations (depth validation)

### Known Issues
- **Annotation placement:** Z-coordinates occasionally inaccurate with models containing multiple nested meshes
- **File upload UX:** No progress bar for large files (requires enhancement)

---

## Summary and Next Steps

### Completed Work

1. **Authentication System** - Login/logout, session management, role-based access
2. **Database Architecture** - 6 relational tables with automated data migration
3. **Catalog CMS** - Full CRUD for PDF catalog management
4. **Video CMS** - Full CRUD for playlists and videos with auto-duration detection
5. **Product CMS** - Full CRUD for products with rich text editor and selective file updates
6. **3D Showroom Integration** - Dynamic data loading from database
7. **Annotation System** - Basic implementation (click-to-place) - under development

### Future Enhancements

**Annotation System**
- Complete drag-and-drop editing
- Additional annotation types (image, video, link)
- Improve Z-depth accuracy

**Performance Optimization**
- Lazy loading for 3D models
- CDN integration for media files
- Database indexing for query optimization
- Image compression for faster loading
- Optimization on video and large GLB files

**Security Enhancements**
- Two-factor authentication (2FA)
- Rate limiting for API endpoints
- CSRF tokens for forms
- File upload virus scanning

**UX Improvements**
- Upload progress bar with percentage
- Drag-and-drop file upload
- Search and filter for admin tables

**Getting Started**
- Interactive tooltips guiding admins through CRUD

---

**Date:** December 2025  

---

## References

**Setup Guide:** [docs/README.md](README.md)  
**Database Schema:** database/schema.sql  
**Database Diagram:** [docs/ERD.png](ERD.png)
