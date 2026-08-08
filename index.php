<?php
// ============================================
// MH Tech Wish Maker - Admin Panel
// ============================================

$dataDir = __DIR__ . '/data/';
$uploadsDir = __DIR__ . '/uploads/';
if (!file_exists($dataDir)) mkdir($dataDir, 0777, true);
if (!file_exists($uploadsDir)) mkdir($uploadsDir, 0777, true);

function loadData($type) {
    global $dataDir;
    $file = $dataDir . $type . '.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return null;
}

function saveData($type, $data) {
    global $dataDir;
    $file = $dataDir . $type . '.json';
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function generateLink($type, $name, $password = '') {
    $baseUrl = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
    $token = bin2hex(random_bytes(16));
    return [
        'link' => $baseUrl . '/view.php?type=' . $type . '&token=' . $token,
        'token' => $token,
        'name' => $name,
        'password' => $password,
        'date' => date('Y-m-d H:i:s')
    ];
}

// Default data
$defaultLoveData = [
    'name' => 'Morgan',
    'title' => 'Love Proposal',
    'questions' => [
        'Kya tum meri "permanent seat" banogi?',
        'Kya tum mere saath chai piyogi aur chandni raat mein ghumogi?',
        'Kya tum meri "queen" banogi?',
        'Kya tum mere saath gana gaogi?',
        'Kya tum mere saath cycle pe ghumne chalogi?',
        'Kya tum mujhe apna dil dogi?'
    ],
    'finalMessage' => 'I LOVE YOU MORGAN! ❤️',
    'colors' => [
        'primary' => '#d66aff',
        'secondary' => '#ff77b0',
        'bg' => 'linear-gradient(145deg, #1a0b1f, #0d0710)'
    ],
    'enableHearts' => true,
    'gallery' => []
];

$defaultBirthdayData = [
    'name' => 'Morgan',
    'age' => 19,
    'title' => 'Happy Birthday!',
    'wishes' => [
        '🎂 Happy Birthday Morgan! 19 ka ho gaye!',
        '🎈 Janamdin mubarak ho! 19 looks beautiful on you!',
        '🎉 Happy Birthday! 19 ka matlab aur smart ho gaye!',
        '🥳 19 ka ho gaye! Aaj enjoy karo full!',
        '💖 Happy Birthday Morgan! 19 aur amazing!'
    ],
    'finalMessage' => 'Best birthday ever! 🎉',
    'colors' => [
        'primary' => '#ff77b0',
        'secondary' => '#d9b3ff',
        'bg' => 'linear-gradient(135deg, #1a0b1f, #2d1340)'
    ],
    'enableHearts' => true,
    'gallery' => []
];

// Load or create main data
$loveData = loadData('love');
if (!$loveData) {
    $loveData = $defaultLoveData;
    $loveData['history'] = [];
    saveData('love', $loveData);
}

$birthdayData = loadData('birthday');
if (!$birthdayData) {
    $birthdayData = $defaultBirthdayData;
    $birthdayData['history'] = [];
    saveData('birthday', $birthdayData);
}

// Handle image upload
function handleImageUpload($type, $token) {
    global $uploadsDir;
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    $uploaded = [];
    
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        $files = $_FILES['gallery_images'];
        $totalFiles = count($files['name']);
        
        for ($i = 0; $i < $totalFiles; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK && !empty($files['tmp_name'][$i])) {
                $name = basename($files['name'][$i]);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (in_array($ext, $allowed)) {
                    $newName = $type . '_' . $token . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                    $path = $uploadsDir . $newName;
                    if (move_uploaded_file($files['tmp_name'][$i], $path)) {
                        $uploaded[] = 'uploads/' . $newName;
                    }
                }
            }
        }
    }
    return $uploaded;
}

// Handle POST
$message = '';
$messageType = '';
$generatedLink = '';
$generatedType = '';
$generatedName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Delete history item
    if ($action === 'delete_history') {
        $type = $_POST['history_type'] ?? '';
        $token = $_POST['history_token'] ?? '';
        $data = loadData($type);
        if ($data && isset($data['history'])) {
            $newHistory = [];
            foreach ($data['history'] as $item) {
                if ($item['token'] !== $token) {
                    $newHistory[] = $item;
                } else {
                    // Delete associated images
                    if (isset($item['settings']['gallery'])) {
                        foreach ($item['settings']['gallery'] as $img) {
                            $fullPath = __DIR__ . '/' . $img;
                            if (file_exists($fullPath)) {
                                unlink($fullPath);
                            }
                        }
                    }
                }
            }
            $data['history'] = $newHistory;
            saveData($type, $data);
            $message = '✅ Link deleted successfully!';
            $messageType = 'success';
        }
        $loveData = loadData('love');
        $birthdayData = loadData('birthday');
    }
    
    // Delete image from gallery
    if ($action === 'delete_image') {
        $type = $_POST['gallery_type'] ?? '';
        $token = $_POST['gallery_token'] ?? '';
        $imagePath = $_POST['image_path'] ?? '';
        
        $data = loadData($type);
        if ($data && isset($data['history'])) {
            foreach ($data['history'] as &$item) {
                if ($item['token'] === $token) {
                    if (isset($item['settings']['gallery'])) {
                        $item['settings']['gallery'] = array_filter($item['settings']['gallery'], function($img) use ($imagePath) {
                            return $img !== $imagePath;
                        });
                        $fullPath = __DIR__ . '/' . $imagePath;
                        if (file_exists($fullPath)) {
                            unlink($fullPath);
                        }
                    }
                    break;
                }
            }
            saveData($type, $data);
            $message = '✅ Image deleted successfully!';
            $messageType = 'success';
        }
        $loveData = loadData('love');
        $birthdayData = loadData('birthday');
    }
    
    if ($action === 'save_love') {
        $name = trim($_POST['love_name'] ?? 'Morgan');
        $password = trim($_POST['love_password'] ?? '');
        $loveData = [
            'name' => $name,
            'title' => trim($_POST['love_title'] ?? 'Love Proposal'),
            'questions' => array_filter(array_map('trim', explode("\n", trim($_POST['love_questions'])))),
            'finalMessage' => trim($_POST['love_final'] ?? 'I LOVE YOU! ❤️'),
            'colors' => [
                'primary' => $_POST['love_color_primary'] ?? '#d66aff',
                'secondary' => $_POST['love_color_secondary'] ?? '#ff77b0',
                'bg' => $_POST['love_color_bg'] ?? 'linear-gradient(145deg, #1a0b1f, #0d0710)'
            ],
            'enableHearts' => isset($_POST['love_hearts']) ? true : false,
            'history' => $loveData['history'] ?? []
        ];
        
        $linkData = generateLink('love', $name, $password);
        
        $uploadedImages = [];
        if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
            $uploadedImages = handleImageUpload('love', $linkData['token']);
        }
        
        $loveData['history'][] = [
            'token' => $linkData['token'],
            'link' => $linkData['link'],
            'name' => $name,
            'password' => $password,
            'date' => $linkData['date'],
            'settings' => [
                'questions' => $loveData['questions'],
                'finalMessage' => $loveData['finalMessage'],
                'colors' => $loveData['colors'],
                'enableHearts' => $loveData['enableHearts'],
                'gallery' => $uploadedImages
            ]
        ];
        
        saveData('love', $loveData);
        $generatedLink = $linkData['link'];
        $generatedType = 'love';
        $generatedName = $name;
        $message = '🎉 Love Proposal generated successfully for ' . htmlspecialchars($name) . '!';
        $messageType = 'success';
    }
    
    elseif ($action === 'save_birthday') {
        $name = trim($_POST['bday_name'] ?? 'Morgan');
        $password = trim($_POST['bday_password'] ?? '');
        $birthdayData = [
            'name' => $name,
            'age' => intval($_POST['bday_age'] ?? 19),
            'title' => trim($_POST['bday_title'] ?? 'Happy Birthday!'),
            'wishes' => array_filter(array_map('trim', explode("\n", trim($_POST['bday_wishes'])))),
            'finalMessage' => trim($_POST['bday_final'] ?? 'Best birthday ever! 🎉'),
            'colors' => [
                'primary' => $_POST['bday_color_primary'] ?? '#ff77b0',
                'secondary' => $_POST['bday_color_secondary'] ?? '#d9b3ff',
                'bg' => $_POST['bday_color_bg'] ?? 'linear-gradient(135deg, #1a0b1f, #2d1340)'
            ],
            'enableHearts' => isset($_POST['bday_hearts']) ? true : false,
            'history' => $birthdayData['history'] ?? []
        ];
        
        $linkData = generateLink('birthday', $name, $password);
        
        $uploadedImages = [];
        if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
            $uploadedImages = handleImageUpload('birthday', $linkData['token']);
        }
        
        $birthdayData['history'][] = [
            'token' => $linkData['token'],
            'link' => $linkData['link'],
            'name' => $name,
            'password' => $password,
            'date' => $linkData['date'],
            'settings' => [
                'age' => $birthdayData['age'],
                'wishes' => $birthdayData['wishes'],
                'finalMessage' => $birthdayData['finalMessage'],
                'colors' => $birthdayData['colors'],
                'enableHearts' => $birthdayData['enableHearts'],
                'gallery' => $uploadedImages
            ]
        ];
        
        saveData('birthday', $birthdayData);
        $generatedLink = $linkData['link'];
        $generatedType = 'birthday';
        $generatedName = $name;
        $message = '🎉 Birthday Wish generated successfully for ' . htmlspecialchars($name) . '!';
        $messageType = 'success';
    }
}

// Load fresh data
$loveData = loadData('love');
$birthdayData = loadData('birthday');
?>
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>MH Tech Wish Maker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            min-height:100vh;
            background: linear-gradient(145deg, #0a0510, #1a0b1f, #0d0710);
            font-family: 'Nunito', sans-serif;
            padding:16px;
            color:#fff;
            position:relative;
            overflow-x:hidden;
        }
        .container {
            max-width:1000px;
            width:100%;
            margin:0 auto;
        }
        
        /* ===== TOP BAR WITH 3-DOT MENU ===== */
        .top-bar {
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:10px 0 20px;
            position:relative;
            z-index:10;
        }
        .top-bar .brand {
            font-family:'Playfair Display',serif;
            font-size:1.3rem;
            background:linear-gradient(135deg,#ffb3e6,#d9b3ff);
            -webkit-background-clip:text;
            background-clip:text;
            color:transparent;
            font-weight:700;
        }
        .top-bar .brand i { color:#ff77b0; margin-right:6px; }
        .top-bar .menu-btn {
            background:rgba(255,255,255,0.03);
            border:1px solid rgba(255,180,255,0.06);
            color:#b397c4;
            width:42px;
            height:42px;
            border-radius:50%;
            font-size:1.2rem;
            cursor:pointer;
            transition:0.3s;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .top-bar .menu-btn:hover {
            background:rgba(255,255,255,0.06);
            color:#fff;
            border-color:rgba(255,180,255,0.12);
        }
        
        /* ===== SIDEBAR ===== */
        .sidebar-overlay {
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.6);
            backdrop-filter:blur(5px);
            z-index:999;
            opacity:0;
            pointer-events:none;
            transition:0.4s;
        }
        .sidebar-overlay.active {
            opacity:1;
            pointer-events:all;
        }
        .sidebar {
            position:fixed;
            top:0;
            right:-320px;
            width:300px;
            height:100%;
            background:rgba(20,10,28,0.95);
            backdrop-filter:blur(30px);
            -webkit-backdrop-filter:blur(30px);
            z-index:1000;
            padding:30px 25px;
            transition:right 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border-left:1px solid rgba(255,180,255,0.04);
            box-shadow:-10px 0 50px rgba(0,0,0,0.5);
            display:flex;
            flex-direction:column;
        }
        .sidebar.open {
            right:0;
        }
        .sidebar .close-sidebar {
            position:absolute;
            top:15px;
            right:20px;
            background:none;
            border:none;
            color:#b397c4;
            font-size:1.5rem;
            cursor:pointer;
            transition:0.3s;
        }
        .sidebar .close-sidebar:hover {
            color:#fff;
            transform:rotate(90deg);
        }
        .sidebar .sidebar-brand {
            font-family:'Playfair Display',serif;
            font-size:1.8rem;
            background:linear-gradient(135deg,#ffb3e6,#d9b3ff);
            -webkit-background-clip:text;
            background-clip:text;
            color:transparent;
            margin-top:20px;
            margin-bottom:4px;
        }
        .sidebar .sidebar-brand i { color:#ff77b0; }
        .sidebar .sidebar-sub {
            color:#6a5080;
            font-size:0.8rem;
            margin-bottom:25px;
            letter-spacing:1px;
        }
        .sidebar .sidebar-sub i { color:#ff77b0; }
        
        .sidebar .divider {
            height:1px;
            background:linear-gradient(to right, rgba(255,180,255,0.05), transparent);
            margin:10px 0 20px;
        }
        
        .sidebar .social-links {
            display:flex;
            flex-direction:column;
            gap:12px;
            flex:1;
        }
        .sidebar .social-links a {
            display:flex;
            align-items:center;
            gap:14px;
            padding:12px 16px;
            border-radius:14px;
            background:rgba(255,255,255,0.02);
            border:1px solid rgba(255,180,255,0.04);
            color:#c8a8e0;
            text-decoration:none;
            transition:0.3s;
            font-weight:600;
            font-size:0.95rem;
        }
        .sidebar .social-links a:hover {
            background:rgba(255,255,255,0.05);
            border-color:rgba(255,180,255,0.1);
            transform:translateX(-5px);
            color:#fff;
        }
        .sidebar .social-links a i {
            font-size:1.3rem;
            width:30px;
            text-align:center;
        }
        .sidebar .social-links a .fa-youtube { color:#ff0000; }
        .sidebar .social-links a .fa-instagram { color:#e4405f; }
        .sidebar .social-links a .fa-facebook { color:#1877f2; }
        .sidebar .social-links a .fa-twitter { color:#1da1f2; }
        .sidebar .social-links a .fa-github { color:#6e5494; }
        .sidebar .social-links a .fa-linkedin { color:#0a66c2; }
        .sidebar .social-links a .fa-whatsapp { color:#25d366; }
        .sidebar .social-links a .fa-telegram { color:#0088cc; }
        
        .sidebar .footer-credit {
            margin-top:20px;
            padding-top:15px;
            border-top:1px solid rgba(255,180,255,0.04);
            color:#6a5080;
            font-size:0.7rem;
            text-align:center;
            letter-spacing:1px;
        }
        .sidebar .footer-credit i { color:#ff77b0; }
        .sidebar .footer-credit .highlight { color:#d9b3ff; font-weight:600; }
        
        /* ===== REST OF THE CSS (same as before) ===== */
        .bg-glow {
            position:fixed;
            border-radius:50%;
            filter:blur(120px);
            pointer-events:none;
            z-index:0;
            animation:glowMove 20s infinite alternate ease-in-out;
        }
        .bg-glow-1 { width:400px; height:400px; background:#d66aff; top:-10%; left:-20%; opacity:0.08; }
        .bg-glow-2 { width:500px; height:500px; background:#ff77b0; bottom:-20%; right:-15%; opacity:0.06; animation-delay:5s; }
        .bg-glow-3 { width:300px; height:300px; background:#9b4dff; top:40%; left:50%; opacity:0.04; animation-delay:10s; }
        
        @keyframes glowMove {
            0% { transform:translate(0,0) scale(1); }
            50% { transform:translate(30px,-20px) scale(1.1); }
            100% { transform:translate(-20px,30px) scale(0.9); }
        }
        
        .landing {
            text-align:center;
            padding:20px 10px;
            position:relative;
            z-index:1;
        }
        .landing .logo {
            font-family: 'Playfair Display', serif;
            font-size:3rem;
            background: linear-gradient(135deg, #ffb3e6, #d9b3ff, #ff77b0);
            -webkit-background-clip:text;
            background-clip:text;
            color:transparent;
            margin-bottom:6px;
            text-shadow:0 0 60px rgba(214,106,255,0.15);
        }
        .landing .logo i { font-size:2.4rem; margin:0 6px; background: linear-gradient(135deg, #ff77b0, #d66aff); -webkit-background-clip:text; background-clip:text; color:transparent; }
        .landing .subtitle {
            color:#b397c4;
            font-size:1rem;
            margin-bottom:30px;
            letter-spacing:2px;
        }
        .landing .subtitle i { color:#ff77b0; }
        
        .btn-landing {
            display:inline-flex;
            align-items:center;
            gap:14px;
            padding:18px 40px;
            border:none;
            border-radius:60px;
            font-family:'Nunito',sans-serif;
            font-weight:800;
            font-size:1.15rem;
            cursor:pointer;
            transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin:8px 12px;
            min-width:200px;
            justify-content:center;
            color:#fff;
            box-shadow:0 10px 40px rgba(0,0,0,0.3);
            position:relative;
            overflow:hidden;
        }
        .btn-landing::after {
            content:'';
            position:absolute;
            top:-50%;
            left:-50%;
            width:200%;
            height:200%;
            background:radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
            opacity:0;
            transition:0.5s;
        }
        .btn-landing:hover::after { opacity:1; }
        .btn-landing i { font-size:1.5rem; }
        .btn-landing:hover { transform:translateY(-5px) scale(1.03); }
        .btn-landing:active { transform:scale(0.95); }
        
        .btn-love { background:linear-gradient(135deg, #d66aff, #9b4dff); box-shadow:0 10px 40px rgba(200,80,255,0.3); }
        .btn-love:hover { box-shadow:0 15px 55px rgba(200,80,255,0.45); }
        .btn-birthday { background:linear-gradient(135deg, #ff77b0, #ff4d8f); box-shadow:0 10px 40px rgba(255,77,143,0.3); }
        .btn-birthday:hover { box-shadow:0 15px 55px rgba(255,77,143,0.45); }
        
        .landing-footer {
            margin-top:40px;
            color:#6a5080;
            font-size:0.85rem;
            letter-spacing:1px;
        }
        .landing-footer i { color:#ff77b0; animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }
        
        /* ===== FORM (same as before) ===== */
        .form-page { display:none; animation:fadeInUp 0.6s ease; position:relative; z-index:1; }
        @keyframes fadeInUp { 0%{opacity:0;transform:translateY(30px);} 100%{opacity:1;transform:translateY(0);} }
        
        .back-btn {
            display:inline-flex;
            align-items:center;
            gap:8px;
            color:#b397c4;
            text-decoration:none;
            font-weight:600;
            font-size:0.9rem;
            margin-bottom:15px;
            transition:0.3s;
            cursor:pointer;
            background:none;
            border:none;
            padding:8px 16px;
            border-radius:30px;
            background:rgba(255,255,255,0.02);
            border:1px solid rgba(255,180,255,0.04);
        }
        .back-btn:hover { color:#ffb3e6; background:rgba(255,255,255,0.04); }
        
        .form-card {
            background:rgba(20,10,28,0.8);
            backdrop-filter:blur(30px);
            -webkit-backdrop-filter:blur(30px);
            border-radius:30px;
            padding:28px 32px;
            border:1px solid rgba(255,180,255,0.06);
            box-shadow:0 25px 80px rgba(0,0,0,0.5);
            max-width:700px;
            margin:0 auto;
            position:relative;
            overflow:hidden;
        }
        .form-card::before {
            content:'';
            position:absolute;
            top:-50%;
            left:-50%;
            width:200%;
            height:200%;
            background:radial-gradient(circle at 30% 20%, rgba(214,106,255,0.03) 0%, transparent 70%);
            pointer-events:none;
        }
        .form-card .form-icon { font-size:2.5rem; margin-bottom:4px; }
        .form-card h2 { font-size:1.7rem; margin-bottom:2px; color:#f0d9ff; }
        .form-card .form-sub { color:#b397c4; font-size:0.85rem; margin-bottom:20px; }
        
        .form-card label {
            display:block;
            color:#c8a8e0;
            font-weight:600;
            font-size:0.8rem;
            margin:14px 0 4px;
            letter-spacing:0.5px;
        }
        .form-card label i { margin-right:6px; color:#ff77b0; }
        .form-card input, .form-card textarea {
            width:100%;
            padding:12px 16px;
            border-radius:14px;
            border:1px solid rgba(255,180,255,0.06);
            background:rgba(255,255,255,0.03);
            color:#f0d9ff;
            font-family:inherit;
            font-size:0.9rem;
            transition:0.3s;
            outline:none;
        }
        .form-card textarea { min-height:70px; resize:vertical; }
        .form-card input:focus, .form-card textarea:focus {
            border-color:#d66aff;
            box-shadow:0 0 30px rgba(200,80,255,0.06);
            background:rgba(255,255,255,0.05);
        }
        .form-card input::placeholder, .form-card textarea::placeholder { color:#6a5080; }
        
        .color-row {
            display:flex;
            gap:10px;
            align-items:center;
            flex-wrap:wrap;
        }
        .color-row input[type="color"] {
            width:50px;
            padding:2px;
            height:42px;
            cursor:pointer;
            border-radius:10px;
            border:1px solid rgba(255,180,255,0.06);
            background:transparent;
        }
        .color-row input[type="text"] { flex:1; min-width:120px; }
        
        .checkbox-row {
            display:flex;
            align-items:center;
            gap:10px;
            margin:14px 0 4px;
            color:#c8a8e0;
            font-size:0.9rem;
        }
        .checkbox-row input[type="checkbox"] {
            width:20px;
            height:20px;
            accent-color:#d66aff;
            cursor:pointer;
        }
        .checkbox-row label { margin:0; cursor:pointer; }
        
        .file-upload-row {
            margin:14px 0 4px;
            padding:20px;
            border:2px dashed rgba(255,180,255,0.1);
            border-radius:16px;
            text-align:center;
            transition:0.3s;
            cursor:pointer;
        }
        .file-upload-row:hover {
            border-color:rgba(214,106,255,0.3);
            background:rgba(255,255,255,0.02);
        }
        .file-upload-row input[type="file"] { display:none; }
        .file-upload-row .upload-label {
            color:#b397c4;
            font-size:0.9rem;
            cursor:pointer;
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:8px;
        }
        .file-upload-row .upload-label i { font-size:2.5rem; color:#6a5080; }
        .file-upload-row .upload-label span { font-weight:600; color:#c8a8e0; }
        .file-upload-row .upload-label small { color:#6a5080; font-size:0.7rem; }
        
        .btn-submit {
            padding:14px 40px;
            border:none;
            border-radius:60px;
            font-family:'Nunito',sans-serif;
            font-weight:800;
            font-size:1.05rem;
            cursor:pointer;
            transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display:inline-flex;
            align-items:center;
            gap:12px;
            margin-top:18px;
            color:#fff;
            background:linear-gradient(135deg, #d66aff, #9b4dff);
            box-shadow:0 6px 30px rgba(200,80,255,0.25);
            position:relative;
            overflow:hidden;
        }
        .btn-submit::after {
            content:'';
            position:absolute;
            top:-50%;
            left:-50%;
            width:200%;
            height:200%;
            background:radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
            opacity:0;
            transition:0.5s;
        }
        .btn-submit:hover::after { opacity:1; }
        .btn-submit:hover { transform:translateY(-4px) scale(1.02); box-shadow:0 10px 45px rgba(200,80,255,0.4); }
        .btn-submit:active { transform:scale(0.95); }
        
        .image-preview-grid {
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(80px, 1fr));
            gap:10px;
            margin:10px 0;
        }
        .image-preview-grid .img-preview {
            width:100%;
            aspect-ratio:1;
            border-radius:12px;
            overflow:hidden;
            border:1px solid rgba(255,180,255,0.06);
            position:relative;
            background:rgba(0,0,0,0.2);
        }
        .image-preview-grid .img-preview img {
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .image-preview-grid .img-preview .remove-img {
            position:absolute;
            top:2px;
            right:2px;
            background:rgba(255,0,0,0.7);
            border:none;
            color:#fff;
            border-radius:50%;
            width:20px;
            height:20px;
            font-size:0.6rem;
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .image-preview-grid .img-preview .img-name {
            position:absolute;
            bottom:0;
            left:0;
            right:0;
            background:rgba(0,0,0,0.6);
            color:#fff;
            font-size:0.5rem;
            padding:2px 4px;
            text-overflow:ellipsis;
            overflow:hidden;
            white-space:nowrap;
        }
        
        .success-card {
            background:rgba(20,10,28,0.8);
            backdrop-filter:blur(30px);
            border-radius:30px;
            padding:28px 32px;
            border:1px solid rgba(100,255,100,0.06);
            box-shadow:0 25px 80px rgba(0,0,0,0.5);
            max-width:700px;
            margin:20px auto 0;
            text-align:center;
            animation:popScale 0.6s ease;
            position:relative;
            z-index:1;
            overflow:hidden;
        }
        @keyframes popScale { 0%{opacity:0;transform:scale(0.9);} 100%{opacity:1;transform:scale(1);} }
        .success-card .success-icon { font-size:3.5rem; margin-bottom:4px; }
        .success-card h3 { font-size:1.5rem; color:#f0d9ff; margin-bottom:2px; }
        .success-card .success-sub { color:#b397c4; font-size:0.85rem; margin-bottom:15px; }
        .success-card .success-sub i { color:#6bdf6b; }
        
        .link-box {
            background:rgba(0,0,0,0.4);
            padding:14px 18px;
            border-radius:14px;
            border:1px solid rgba(255,180,255,0.04);
            margin-bottom:18px;
            word-break:break-all;
            font-family:monospace;
            font-size:0.8rem;
            color:#ffb3e6;
            position:relative;
        }
        .link-box .link-label {
            position:absolute;
            top:-10px;
            left:15px;
            background:rgba(20,10,28,0.9);
            padding:0 10px;
            font-size:0.65rem;
            color:#6a5080;
            font-family:'Nunito',sans-serif;
            letter-spacing:1px;
        }
        
        .btn-group-success {
            display:flex;
            gap:12px;
            justify-content:center;
            flex-wrap:wrap;
        }
        .btn-group-success .btn {
            padding:10px 25px;
            border:none;
            border-radius:50px;
            font-family:'Nunito',sans-serif;
            font-weight:700;
            font-size:0.85rem;
            cursor:pointer;
            transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display:inline-flex;
            align-items:center;
            gap:8px;
            text-decoration:none;
            color:#fff;
        }
        .btn-group-success .btn:hover { transform:translateY(-3px); }
        .btn-group-success .btn:active { transform:scale(0.95); }
        .btn-group-success .btn-primary { background:linear-gradient(135deg, #d66aff, #9b4dff); box-shadow:0 4px 20px rgba(200,80,255,0.2); }
        .btn-group-success .btn-primary:hover { box-shadow:0 8px 35px rgba(200,80,255,0.35); }
        .btn-group-success .btn-secondary { background:rgba(255,255,255,0.05); border:1px solid rgba(255,180,255,0.06); color:#d9b3ff; }
        .btn-group-success .btn-secondary:hover { background:rgba(255,255,255,0.08); }
        .btn-group-success .btn-success { background:linear-gradient(135deg, #6bdf6b, #4caf50); box-shadow:0 4px 20px rgba(100,255,100,0.15); }
        .btn-group-success .btn-success:hover { box-shadow:0 8px 35px rgba(100,255,100,0.3); }
        
        .history-section {
            margin-top:35px;
            max-width:700px;
            margin-left:auto;
            margin-right:auto;
            position:relative;
            z-index:1;
        }
        .history-section .history-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:12px;
            padding:0 4px;
        }
        .history-section h4 {
            color:#c8a8e0;
            font-size:1rem;
        }
        .history-section h4 i { color:#ff77b0; margin-right:8px; }
        .history-section .history-count {
            font-size:0.7rem;
            color:#6a5080;
            background:rgba(255,255,255,0.03);
            padding:2px 12px;
            border-radius:20px;
            border:1px solid rgba(255,180,255,0.04);
        }
        
        .history-grid {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
        }
        @media (max-width:600px) {
            .history-grid { grid-template-columns:1fr; }
        }
        
        .history-item {
            background:rgba(20,10,28,0.6);
            backdrop-filter:blur(15px);
            padding:14px 16px;
            border-radius:18px;
            border:1px solid rgba(255,180,255,0.04);
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
            transition:0.3s;
            animation:fadeInUp 0.4s ease;
        }
        .history-item:hover {
            border-color:rgba(255,180,255,0.1);
            background:rgba(20,10,28,0.7);
        }
        .history-item .h-info {
            flex:1;
            min-width:100px;
        }
        .history-item .h-name {
            font-weight:700;
            color:#f0d9ff;
            font-size:0.95rem;
            display:flex;
            align-items:center;
            gap:6px;
            flex-wrap:wrap;
        }
        .history-item .h-name .h-icon {
            font-size:0.7rem;
            width:24px;
            height:24px;
            border-radius:50%;
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }
        .history-item .h-name .h-icon.love { background:rgba(214,106,255,0.15); color:#d66aff; }
        .history-item .h-name .h-icon.birthday { background:rgba(255,119,176,0.15); color:#ff77b0; }
        .history-item .h-date {
            color:#6a5080;
            font-size:0.65rem;
            margin-top:2px;
        }
        .history-item .h-password {
            color:#9b7aaa;
            font-size:0.6rem;
            background:rgba(255,255,255,0.02);
            padding:0 8px;
            border-radius:10px;
            border:1px solid rgba(255,180,255,0.03);
            display:inline-block;
        }
        .history-item .h-gallery-badge {
            color:#6a5080;
            font-size:0.6rem;
            background:rgba(255,255,255,0.02);
            padding:0 8px;
            border-radius:10px;
            border:1px solid rgba(255,180,255,0.03);
            display:inline-block;
        }
        .history-item .h-actions {
            display:flex;
            gap:6px;
            flex-wrap:wrap;
        }
        .history-item .h-actions .h-btn {
            padding:5px 12px;
            border:none;
            border-radius:20px;
            font-size:0.7rem;
            font-weight:700;
            cursor:pointer;
            transition:all 0.3s;
            color:#fff;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            gap:4px;
        }
        .history-item .h-actions .h-btn-copy { background:rgba(255,255,255,0.05); color:#b397c4; }
        .history-item .h-actions .h-btn-copy:hover { background:rgba(255,255,255,0.1); }
        .history-item .h-actions .h-btn-open { background:linear-gradient(135deg, #6bdf6b, #4caf50); }
        .history-item .h-actions .h-btn-open:hover { transform:scale(1.05); }
        .history-item .h-actions .h-btn-delete { background:rgba(255,77,77,0.15); color:#ff6b6b; border:1px solid rgba(255,77,77,0.08); }
        .history-item .h-actions .h-btn-delete:hover { background:rgba(255,77,77,0.25); transform:scale(1.05); }
        
        .history-empty {
            color:#6a5080;
            text-align:center;
            font-size:0.9rem;
            padding:30px;
            background:rgba(255,255,255,0.01);
            border-radius:20px;
            border:1px dashed rgba(255,180,255,0.04);
        }
        .history-empty i { color:#ff77b0; font-size:2rem; display:block; margin-bottom:10px; }
        
        .floating-hearts {
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            pointer-events:none;
            z-index:0;
            overflow:hidden;
        }
        .float-heart {
            position:absolute;
            font-size:1.2rem;
            opacity:0.03;
            animation:floatUp linear infinite;
            pointer-events:none;
        }
        @keyframes floatUp {
            0% { transform:translateY(100vh) scale(0.5) rotate(0deg); opacity:0; }
            10% { opacity:0.03; }
            90% { opacity:0.03; }
            100% { transform:translateY(-10vh) scale(1) rotate(360deg); opacity:0; }
        }
        
        .toast {
            position:fixed; bottom:30px; left:50%; transform:translateX(-50%);
            background:rgba(20,10,28,0.95); backdrop-filter:blur(20px);
            padding:14px 30px; border-radius:60px;
            color:#fff; font-family:'Nunito',sans-serif; font-weight:600; font-size:0.9rem;
            border:1px solid rgba(255,180,255,0.08);
            box-shadow:0 15px 50px rgba(0,0,0,0.6);
            z-index:9999;
            animation:toastAnim 2.8s ease forwards;
        }
        @keyframes toastAnim {
            0% { opacity:0; transform:translateX(-50%) translateY(30px) scale(0.9); }
            15% { opacity:1; transform:translateX(-50%) translateY(0) scale(1); }
            85% { opacity:1; transform:translateX(-50%) translateY(0) scale(1); }
            100% { opacity:0; transform:translateX(-50%) translateY(30px) scale(0.9); }
        }
        
        @media (max-width:480px) {
            .landing .logo { font-size:2.2rem; }
            .landing .logo i { font-size:1.8rem; }
            .btn-landing { padding:14px 24px; font-size:0.9rem; min-width:160px; margin:6px; }
            .btn-landing i { font-size:1.2rem; }
            .form-card { padding:18px 16px; border-radius:22px; }
            .form-card h2 { font-size:1.3rem; }
            .success-card { padding:18px 16px; border-radius:22px; }
            .success-card h3 { font-size:1.2rem; }
            .link-box { font-size:0.7rem; padding:10px 12px; }
            .btn-group-success .btn { padding:8px 16px; font-size:0.8rem; }
            .history-item { padding:10px 12px; }
            .history-item .h-name { font-size:0.85rem; }
            .sidebar { width:280px; right:-300px; }
            .top-bar .brand { font-size:1.1rem; }
        }
        @media (max-width:380px) {
            .landing .logo { font-size:1.8rem; }
            .landing .logo i { font-size:1.4rem; }
            .btn-landing { padding:12px 16px; font-size:0.8rem; min-width:140px; }
            .form-card { padding:12px; }
            .form-card h2 { font-size:1.1rem; }
            .sidebar { width:260px; right:-280px; }
        }
    </style>
</head>
<body>

<!-- Background Glows -->
<div class="bg-glow bg-glow-1"></div>
<div class="bg-glow bg-glow-2"></div>
<div class="bg-glow bg-glow-3"></div>

<!-- Floating Hearts -->
<div class="floating-hearts" id="floatingHearts"></div>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<div class="sidebar" id="sidebar">
    <button class="close-sidebar" onclick="closeSidebar()"><i class="fas fa-times"></i></button>
    <div class="sidebar-brand">
        <i class="fas fa-magic"></i> MH Tech
    </div>
    <div class="sidebar-sub"><i class="fas fa-heart"></i> Wish Maker</div>
    
    <div class="divider"></div>
    
    <div class="social-links">
        <a href="https://youtube.com/@mhtechteam" target="_blank"><i class="fab fa-youtube"></i> YouTube</a>
        <a href="https://tiktok.com/@mhtechteam.official" target="_blank"><i class="fab fa-tiktok"></i> Tik Tok</a>
        <a href="https://whatsapp.com/channel/0029Vb6ZQPa5q08cHvIQDW2g" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
        <a href="https://t.me/mhtechteam" target="_blank"><i class="fab fa-telegram"></i> Telegram</a>
    </div>
    
    <div class="footer-credit">
        <i class="fas fa-crown"></i> Created by <span class="highlight">Mr. Hanan</span>
    </div>
</div>

<div class="container">

    <!-- ===== TOP BAR ===== -->
    <div class="top-bar">
        <div class="brand">
            <i class="fas fa-magic"></i> MH Tech Wish Maker
        </div>
        <button class="menu-btn" onclick="openSidebar()">
            <i class="fas fa-ellipsis-v"></i>
        </button>
    </div>

    <!-- ===== LANDING ===== -->
    <div class="landing" id="landingPage" style="display:<?php echo isset($_POST['action']) && $_POST['action'] !== 'delete_history' && $_POST['action'] !== 'delete_image' ? 'none' : 'block'; ?>;">
        <div class="logo">
            <i class="fas fa-magic"></i> MH Tech <i class="fas fa-heart"></i> Wish Maker
        </div>
        <div class="subtitle">
            <i class="fas fa-star"></i> Create beautiful wishes for your loved ones <i class="fas fa-star"></i>
        </div>
        
        <div>
            <button class="btn-landing btn-love" onclick="showForm('love')">
                <i class="fas fa-heart"></i> Generate Love Proposal
            </button>
            <button class="btn-landing btn-birthday" onclick="showForm('birthday')">
                <i class="fas fa-gift"></i> Generate Happy Birthday
            </button>
        </div>
        
        <div class="landing-footer">
            <i class="fas fa-crown"></i> Made with love for your special someone <i class="fas fa-crown"></i>
        </div>
    </div>

    <!-- ===== FORM ===== -->
    <div class="form-page" id="formPage">
        <div id="loveForm" style="display:none;">
            <button class="back-btn" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>
            <div class="form-card">
                <div class="form-icon">💕</div>
                <h2>Generate Love Proposal</h2>
                <div class="form-sub">Customize your love proposal page with images</div>
                <form method="POST" id="loveFormSubmit" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_love">
                    <label><i class="fas fa-user"></i> Name</label>
                    <input type="text" name="love_name" value="<?php echo htmlspecialchars($loveData['name']); ?>" placeholder="Enter name...">
                    <label><i class="fas fa-lock"></i> Set Password (optional)</label>
                    <input type="text" name="love_password" placeholder="Leave empty for no password">
                    <label><i class="fas fa-tag"></i> Title</label>
                    <input type="text" name="love_title" value="<?php echo htmlspecialchars($loveData['title']); ?>" placeholder="Love Proposal">
                    <label><i class="fas fa-question-circle"></i> Questions (one per line)</label>
                    <textarea name="love_questions" rows="5" placeholder="Enter each question on a new line..."><?php echo htmlspecialchars(implode("\n", $loveData['questions'])); ?></textarea>
                    <label><i class="fas fa-heart"></i> Final Message</label>
                    <input type="text" name="love_final" value="<?php echo htmlspecialchars($loveData['finalMessage']); ?>" placeholder="I LOVE YOU! ❤️">
                    <label><i class="fas fa-palette"></i> Colors</label>
                    <div class="color-row">
                        <input type="color" name="love_color_primary" value="<?php echo $loveData['colors']['primary']; ?>">
                        <input type="color" name="love_color_secondary" value="<?php echo $loveData['colors']['secondary']; ?>">
                        <input type="text" name="love_color_bg" value="<?php echo htmlspecialchars($loveData['colors']['bg']); ?>" placeholder="background gradient">
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" name="love_hearts" <?php echo $loveData['enableHearts'] ? 'checked' : ''; ?>>
                        <label>Enable floating hearts animation</label>
                    </div>
                    
                    <label><i class="fas fa-images"></i> Upload Gallery Images</label>
                    <div class="file-upload-row" onclick="document.getElementById('love_images').click()">
                        <input type="file" name="gallery_images[]" id="love_images" multiple accept="image/*" onchange="previewImages(this, 'love_preview')">
                        <div class="upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to select images</span>
                            <small>Supported: JPG, PNG, GIF, WebP, SVG (Multiple allowed)</small>
                        </div>
                    </div>
                    <div class="image-preview-grid" id="love_preview"></div>
                    
                    <button type="submit" class="btn-submit"><i class="fas fa-magic"></i> Generate Link</button>
                </form>
            </div>
        </div>
        
        <div id="birthdayForm" style="display:none;">
            <button class="back-btn" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>
            <div class="form-card">
                <div class="form-icon">🎂</div>
                <h2>Generate Happy Birthday</h2>
                <div class="form-sub">Customize your birthday wish page with images</div>
                <form method="POST" id="birthdayFormSubmit" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="save_birthday">
                    <label><i class="fas fa-user"></i> Name</label>
                    <input type="text" name="bday_name" value="<?php echo htmlspecialchars($birthdayData['name']); ?>" placeholder="Enter name...">
                    <label><i class="fas fa-lock"></i> Set Password (optional)</label>
                    <input type="text" name="bday_password" placeholder="Leave empty for no password">
                    <label><i class="fas fa-calendar-alt"></i> Age</label>
                    <input type="number" name="bday_age" value="<?php echo $birthdayData['age']; ?>" placeholder="19">
                    <label><i class="fas fa-tag"></i> Title</label>
                    <input type="text" name="bday_title" value="<?php echo htmlspecialchars($birthdayData['title']); ?>" placeholder="Happy Birthday!">
                    <label><i class="fas fa-gift"></i> Wishes (one per line)</label>
                    <textarea name="bday_wishes" rows="5" placeholder="Enter each wish on a new line..."><?php echo htmlspecialchars(implode("\n", $birthdayData['wishes'])); ?></textarea>
                    <label><i class="fas fa-heart"></i> Final Message</label>
                    <input type="text" name="bday_final" value="<?php echo htmlspecialchars($birthdayData['finalMessage']); ?>" placeholder="Best birthday ever! 🎉">
                    <label><i class="fas fa-palette"></i> Colors</label>
                    <div class="color-row">
                        <input type="color" name="bday_color_primary" value="<?php echo $birthdayData['colors']['primary']; ?>">
                        <input type="color" name="bday_color_secondary" value="<?php echo $birthdayData['colors']['secondary']; ?>">
                        <input type="text" name="bday_color_bg" value="<?php echo htmlspecialchars($birthdayData['colors']['bg']); ?>" placeholder="background gradient">
                    </div>
                    <div class="checkbox-row">
                        <input type="checkbox" name="bday_hearts" <?php echo $birthdayData['enableHearts'] ? 'checked' : ''; ?>>
                        <label>Enable floating hearts animation</label>
                    </div>
                    
                    <label><i class="fas fa-images"></i> Upload Gallery Images</label>
                    <div class="file-upload-row" onclick="document.getElementById('bday_images').click()">
                        <input type="file" name="gallery_images[]" id="bday_images" multiple accept="image/*" onchange="previewImages(this, 'bday_preview')">
                        <div class="upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Click to select images</span>
                            <small>Supported: JPG, PNG, GIF, WebP, SVG (Multiple allowed)</small>
                        </div>
                    </div>
                    <div class="image-preview-grid" id="bday_preview"></div>
                    
                    <button type="submit" class="btn-submit"><i class="fas fa-magic"></i> Generate Link</button>
                </form>
            </div>
        </div>
    </div>

    <!-- ===== SUCCESS ===== -->
    <?php if ($message && $generatedLink && $_POST['action'] !== 'delete_history' && $_POST['action'] !== 'delete_image'): ?>
    <div class="success-card" id="successCard">
        <div class="success-icon"><?php echo $generatedType === 'love' ? '💕' : '🎂'; ?></div>
        <h3><?php echo $generatedType === 'love' ? 'Love Proposal Generated!' : 'Birthday Wish Generated!'; ?></h3>
        <div class="success-sub"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?></div>
        <div class="link-box">
            <span class="link-label"><i class="fas fa-link"></i> SHAREABLE LINK</span>
            <?php echo htmlspecialchars($generatedLink); ?>
        </div>
        <div class="btn-group-success">
            <button class="btn btn-primary" onclick="copyLink()"><i class="fas fa-copy"></i> Copy</button>
            <a href="<?php echo htmlspecialchars($generatedLink); ?>" target="_blank" class="btn btn-success"><i class="fas fa-external-link-alt"></i> Open</a>
            <button class="btn btn-secondary" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===== HISTORY ===== -->
    <?php
    $loveHistory = $loveData['history'] ?? [];
    $birthdayHistory = $birthdayData['history'] ?? [];
    $allHistory = array_merge(
        array_map(function($h) { $h['type'] = 'love'; return $h; }, $loveHistory),
        array_map(function($h) { $h['type'] = 'birthday'; return $h; }, $birthdayHistory)
    );
    usort($allHistory, function($a, $b) { return strtotime($b['date']) - strtotime($a['date']); });
    $allHistory = array_slice($allHistory, 0, 15);
    ?>
    
    <div class="history-section">
        <div class="history-header">
            <h4><i class="fas fa-clock"></i> Recent Generations</h4>
            <span class="history-count"><?php echo count($allHistory); ?> links</span>
        </div>
        
        <?php if (!empty($allHistory)): ?>
        <div class="history-grid">
            <?php foreach ($allHistory as $item): 
                $galleryCount = isset($item['settings']['gallery']) ? count($item['settings']['gallery']) : 0;
            ?>
            <div class="history-item">
                <div class="h-info">
                    <div class="h-name">
                        <span class="h-icon <?php echo $item['type'] === 'love' ? 'love' : 'birthday'; ?>">
                            <i class="fas fa-<?php echo $item['type'] === 'love' ? 'heart' : 'gift'; ?>"></i>
                        </span>
                        <?php echo htmlspecialchars($item['name']); ?>
                        <?php if (!empty($item['password'])): ?>
                            <span class="h-password"><i class="fas fa-lock"></i> protected</span>
                        <?php endif; ?>
                        <?php if ($galleryCount > 0): ?>
                            <span class="h-gallery-badge"><i class="fas fa-images"></i> <?php echo $galleryCount; ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="h-date"><i class="far fa-calendar-alt"></i> <?php echo date('d M Y, h:i A', strtotime($item['date'])); ?></div>
                </div>
                <div class="h-actions">
                    <button class="h-btn h-btn-copy" onclick="copyCustomLink('<?php echo htmlspecialchars($item['link']); ?>')">
                        <i class="fas fa-copy"></i>
                    </button>
                    <a href="<?php echo htmlspecialchars($item['link']); ?>" target="_blank" class="h-btn h-btn-open">
                        <i class="fas fa-external-link-alt"></i> Open
                    </a>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('⚠️ Are you sure you want to delete this link? It will expire permanently!');">
                        <input type="hidden" name="action" value="delete_history">
                        <input type="hidden" name="history_type" value="<?php echo $item['type']; ?>">
                        <input type="hidden" name="history_token" value="<?php echo $item['token']; ?>">
                        <button type="submit" class="h-btn h-btn-delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="history-empty">
            <i class="fas fa-heart"></i>
            No links generated yet. Create your first wish!
        </div>
        <?php endif; ?>
    </div>

</div>

<script>
// Sidebar Functions
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('active');
    document.body.style.overflow = '';
}

// Image Preview
function previewImages(input, previewId) {
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';
    if (input.files) {
        Array.from(input.files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'img-preview';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="${file.name}">
                    <button class="remove-img" onclick="this.parentElement.remove()">✕</button>
                    <div class="img-name">${file.name}</div>
                `;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        });
    }
}

// Navigation
function showForm(type) {
    document.getElementById('landingPage').style.display = 'none';
    document.getElementById('formPage').style.display = 'block';
    document.getElementById('loveForm').style.display = type === 'love' ? 'block' : 'none';
    document.getElementById('birthdayForm').style.display = type === 'birthday' ? 'block' : 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    closeSidebar();
}

function goBack() {
    document.getElementById('landingPage').style.display = 'block';
    document.getElementById('formPage').style.display = 'none';
    document.getElementById('loveForm').style.display = 'none';
    document.getElementById('birthdayForm').style.display = 'none';
    const successCard = document.getElementById('successCard');
    if (successCard) successCard.style.display = 'none';
    window.scrollTo({ top: 0, behavior: 'smooth' });
    closeSidebar();
}

function copyLink() {
    const linkBox = document.querySelector('.link-box');
    if (!linkBox) return;
    const text = linkBox.textContent.replace('SHAREABLE LINK', '').trim();
    copyText(text);
}

function copyCustomLink(url) {
    copyText(url);
}

function copyText(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => showToast('✅ Link copied!'));
    } else {
        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('✅ Link copied!');
    }
}

function showToast(msg) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => { if (toast.parentNode) toast.remove(); }, 2800);
}

// Floating Hearts
(function() {
    const container = document.getElementById('floatingHearts');
    const hearts = ['♥', '♡', '❤', '💕', '💗'];
    function createHeart() {
        const el = document.createElement('div');
        el.className = 'float-heart';
        el.textContent = hearts[Math.floor(Math.random() * hearts.length)];
        el.style.left = Math.random() * 100 + '%';
        el.style.fontSize = (0.6 + Math.random() * 1) + 'rem';
        el.style.animationDuration = (15 + Math.random() * 20) + 's';
        el.style.animationDelay = Math.random() * 10 + 's';
        el.style.opacity = 0.02 + Math.random() * 0.03;
        el.style.color = `hsl(${320 + Math.random() * 40}, 80%, 70%)`;
        container.appendChild(el);
        setTimeout(() => { if (el.parentNode) el.remove(); }, 35000);
    }
    for (let i = 0; i < 12; i++) setTimeout(createHeart, i * 200);
    setInterval(createHeart, 900);
})();

<?php if (isset($_POST['action']) && $_POST['action'] !== 'delete_history' && $_POST['action'] !== 'delete_image'): ?>
    <?php if ($_POST['action'] === 'save_love'): ?>
    document.addEventListener('DOMContentLoaded', function() { showForm('love'); });
    <?php elseif ($_POST['action'] === 'save_birthday'): ?>
    document.addEventListener('DOMContentLoaded', function() { showForm('birthday'); });
    <?php endif; ?>
<?php endif; ?>
</script>
</body>
</html>