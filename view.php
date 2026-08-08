<?php
// ============================================
// MH Tech Wish Maker - Frontend Viewer
// ============================================

$dataDir = __DIR__ . '/data/';

function loadData($type) {
    global $dataDir;
    $file = $dataDir . $type . '.json';
    if (file_exists($file)) {
        return json_decode(file_get_contents($file), true);
    }
    return null;
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

$data = null;
$valid = false;
$settings = null;
$storedPassword = '';

if ($type === 'love') {
    $data = loadData('love');
    if ($data && isset($data['history'])) {
        foreach ($data['history'] as $item) {
            if ($item['token'] === $token) {
                $valid = true;
                $settings = $item['settings'];
                $settings['name'] = $item['name'];
                $storedPassword = $item['password'] ?? '';
                break;
            }
        }
    }
} elseif ($type === 'birthday') {
    $data = loadData('birthday');
    if ($data && isset($data['history'])) {
        foreach ($data['history'] as $item) {
            if ($item['token'] === $token) {
                $valid = true;
                $settings = $item['settings'];
                $settings['name'] = $item['name'];
                $storedPassword = $item['password'] ?? '';
                break;
            }
        }
    }
}

if (!$valid || !$settings) {
    die('
    <!DOCTYPE html>
    <html>
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Invalid Link</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{min-height:100vh;background:linear-gradient(145deg,#1a0b1f,#0d0710);display:flex;align-items:center;justify-content:center;font-family:"Nunito",sans-serif;padding:20px;text-align:center;}
        div{background:rgba(20,10,28,0.85);padding:40px 30px;border-radius:30px;border:1px solid rgba(255,180,255,0.06);max-width:400px;}
        h2{color:#b397c4;font-size:1.5rem;margin-bottom:10px;}
        p{color:#6a5080;font-size:0.9rem;}
        i{color:#ff6b6b;font-size:3rem;display:block;margin-bottom:15px;}
    </style>
    </head>
    <body><div><i class="fas fa-exclamation-circle"></i><h2>❌ Link Not Found</h2><p>This link has been deleted or expired. Please generate a new one.</p></div></body>
    </html>
    ');
}

// Password check
if (!empty($storedPassword)) {
    $enteredPassword = isset($_POST['password']) ? trim($_POST['password']) : '';
    if (empty($enteredPassword) && !isset($_GET['auth'])) {
        ?>
        <!DOCTYPE html>
        <html lang="hi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
            <title>🔒 Protected Page</title>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <style>
                *{margin:0;padding:0;box-sizing:border-box;}
                body{min-height:100vh;background:linear-gradient(145deg,#1a0b1f,#0d0710);display:flex;align-items:center;justify-content:center;font-family:'Nunito',sans-serif;padding:20px;}
                .password-box{background:rgba(20,10,28,0.85);backdrop-filter:blur(30px);padding:40px 35px;border-radius:30px;border:1px solid rgba(255,180,255,0.06);max-width:400px;width:100%;text-align:center;box-shadow:0 25px 80px rgba(0,0,0,0.5);animation:fadeInUp 0.6s ease;}
                @keyframes fadeInUp{0%{opacity:0;transform:translateY(30px);}100%{opacity:1;transform:translateY(0);}}
                .password-box .lock-icon{font-size:3.5rem;color:#d66aff;margin-bottom:10px;}
                .password-box h2{color:#f0d9ff;font-size:1.5rem;margin-bottom:4px;}
                .password-box .sub{color:#b397c4;font-size:0.9rem;margin-bottom:20px;}
                .password-box input{width:100%;padding:14px 18px;border-radius:14px;border:1px solid rgba(255,180,255,0.06);background:rgba(255,255,255,0.03);color:#f0d9ff;font-size:1rem;outline:none;transition:0.3s;margin-bottom:12px;}
                .password-box input:focus{border-color:#d66aff;box-shadow:0 0 30px rgba(200,80,255,0.06);}
                .password-box input::placeholder{color:#6a5080;}
                .password-box .btn{width:100%;padding:14px;border:none;border-radius:14px;background:linear-gradient(135deg,#d66aff,#9b4dff);color:#fff;font-size:1.05rem;font-weight:700;cursor:pointer;transition:0.3s;font-family:'Nunito',sans-serif;}
                .password-box .btn:hover{transform:translateY(-3px);box-shadow:0 8px 35px rgba(200,80,255,0.3);}
                .password-box .error{color:#ff6b6b;font-size:0.85rem;margin-top:8px;}
                .password-box .hint{color:#6a5080;font-size:0.75rem;margin-top:12px;}
                .bg-glow{position:fixed;border-radius:50%;filter:blur(120px);pointer-events:none;z-index:0;}
                .bg-glow-1{width:300px;height:300px;background:#d66aff;top:-10%;left:-20%;opacity:0.06;}
                .bg-glow-2{width:400px;height:400px;background:#ff77b0;bottom:-20%;right:-15%;opacity:0.05;}
            </style>
        </head>
        <body>
            <div class="bg-glow bg-glow-1"></div>
            <div class="bg-glow bg-glow-2"></div>
            <div class="password-box">
                <div class="lock-icon"><i class="fas fa-lock"></i></div>
                <h2>🔒 Protected Page</h2>
                <div class="sub">This page is password protected. Enter the password to continue.</div>
                <form method="POST">
                    <input type="password" name="password" placeholder="Enter password..." autofocus>
                    <?php if (isset($_GET['error'])): ?>
                        <div class="error"><i class="fas fa-exclamation-circle"></i> Incorrect password. Try again.</div>
                    <?php endif; ?>
                    <button type="submit" class="btn"><i class="fas fa-unlock-alt"></i> Unlock</button>
                </form>
                <div class="hint"><i class="fas fa-key"></i> Contact the owner for password</div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
    if ($enteredPassword !== $storedPassword) {
        header('Location: ' . $_SERVER['REQUEST_URI'] . '&error=1');
        exit;
    }
}

// ============================================
// RENDER LOVE PROPOSAL
// ============================================
if ($type === 'love') {
    $name = $settings['name'] ?? 'Morgan';
    $questions = $settings['questions'] ?? ['Kya tum meri "permanent seat" banogi?'];
    $finalMessage = $settings['finalMessage'] ?? 'I LOVE YOU MORGAN! ❤️';
    $primary = $settings['colors']['primary'] ?? '#d66aff';
    $secondary = $settings['colors']['secondary'] ?? '#ff77b0';
    $bg = $settings['colors']['bg'] ?? 'linear-gradient(145deg, #1a0b1f, #0d0710)';
    $enableHearts = $settings['enableHearts'] ?? true;
    $gallery = $settings['gallery'] ?? [];
    $hasGallery = !empty($gallery);
    ?>
    <!DOCTYPE html>
    <html lang="hi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
        <title>💕 Love Proposal</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            html, body {
                min-height:100vh;
                background: <?php echo $bg; ?>;
                display:flex;
                align-items:center;
                justify-content:center;
                font-family:'Nunito',sans-serif;
                padding:10px;
                position:relative;
                overflow-x:hidden;
            }
            .orb {
                position:fixed;
                border-radius:50%;
                filter:blur(80px);
                opacity:0.10;
                pointer-events:none;
                z-index:0;
                animation:orbFloat 15s infinite alternate ease-in-out;
            }
            @keyframes orbFloat { 0%{transform:translate(0,0) scale(1);} 100%{transform:translate(30px,-20px) scale(1.1);} }
            .orb1 { width:200px; height:200px; background:<?php echo $primary; ?>; top:-10%; left:-10%; }
            .orb2 { width:250px; height:250px; background:<?php echo $secondary; ?>; bottom:-15%; right:-10%; animation-delay:5s; }
            
            .proposal-card {
                position:relative;
                z-index:10;
                max-width:420px;
                width:100%;
                background:rgba(20,10,28,0.88);
                backdrop-filter:blur(30px);
                -webkit-backdrop-filter:blur(30px);
                border-radius:28px;
                padding:1.6rem 1.2rem;
                text-align:center;
                border:1px solid rgba(255,180,255,0.06);
                box-shadow:0 25px 80px rgba(0,0,0,0.5);
                animation:fadeScale 0.6s ease;
                max-height:95vh;
                overflow-y:auto;
            }
            .proposal-card::-webkit-scrollbar { width:3px; }
            .proposal-card::-webkit-scrollbar-track { background:transparent; }
            .proposal-card::-webkit-scrollbar-thumb { background:rgba(255,180,255,0.15); border-radius:10px; }
            @keyframes fadeScale { 0%{opacity:0;transform:scale(0.95);} 100%{opacity:1;transform:scale(1);} }
            
            .morgan-name {
                font-family:'Playfair Display',serif;
                font-weight:700;
                font-size:2.4rem;
                letter-spacing:2px;
                background:linear-gradient(135deg, #ffd9f0, <?php echo $primary; ?>, <?php echo $secondary; ?>);
                -webkit-background-clip:text;
                background-clip:text;
                color:transparent;
                line-height:1.1;
                margin-bottom:2px;
                text-shadow:0 0 40px rgba(214,106,255,0.1);
            }
            .morgan-name i { font-size:1.8rem; margin:0 4px; background:linear-gradient(135deg, <?php echo $secondary; ?>, <?php echo $primary; ?>); -webkit-background-clip:text; background-clip:text; color:transparent; }
            .sub-head {
                font-size:0.7rem;
                color:#c8a8e0;
                letter-spacing:4px;
                font-weight:400;
                margin-bottom:1rem;
                border-bottom:1px solid rgba(255,180,255,0.06);
                padding-bottom:0.5rem;
                display:inline-block;
            }
            
            <?php if ($hasGallery): ?>
            .gallery-section {
                margin:0.6rem 0 1rem;
                border-radius:16px;
                background:rgba(0,0,0,0.25);
                border:1px solid rgba(255,180,255,0.06);
                overflow:hidden;
                position:relative;
                padding:8px 0;
                box-shadow:inset 0 0 30px rgba(0,0,0,0.2);
            }
            .gallery-scroll {
                display:flex;
                overflow-x:auto;
                scroll-behavior:smooth;
                gap:14px;
                padding:6px 14px;
                scroll-snap-type:x mandatory;
                -webkit-overflow-scrolling:touch;
                cursor:grab;
                scrollbar-width:thin;
                padding-bottom:10px;
            }
            .gallery-scroll::-webkit-scrollbar { height:4px; }
            .gallery-scroll::-webkit-scrollbar-track { background:rgba(255,255,255,0.03); border-radius:10px; }
            .gallery-scroll::-webkit-scrollbar-thumb { background:linear-gradient(90deg, <?php echo $primary; ?>, <?php echo $secondary; ?>); border-radius:10px; }
            .gallery-scroll:active { cursor:grabbing; }
            
            .gallery-item {
                min-width:120px;
                max-width:150px;
                flex-shrink:0;
                aspect-ratio:1;
                border-radius:16px;
                overflow:hidden;
                scroll-snap-align:center;
                border:2px solid rgba(255,180,255,0.06);
                transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                background:rgba(0,0,0,0.3);
                position:relative;
                transform:scale(0.95);
                animation:imgAppear 0.6s ease forwards;
                cursor:pointer;
                box-shadow:0 4px 15px rgba(0,0,0,0.2);
            }
            .gallery-item:nth-child(1) { animation-delay:0.1s; }
            .gallery-item:nth-child(2) { animation-delay:0.2s; }
            .gallery-item:nth-child(3) { animation-delay:0.3s; }
            .gallery-item:nth-child(4) { animation-delay:0.4s; }
            .gallery-item:nth-child(5) { animation-delay:0.5s; }
            .gallery-item:nth-child(6) { animation-delay:0.6s; }
            .gallery-item:nth-child(7) { animation-delay:0.7s; }
            .gallery-item:nth-child(8) { animation-delay:0.8s; }
            
            @keyframes imgAppear {
                0% { transform:scale(0.8) rotate(-5deg); opacity:0; }
                60% { transform:scale(1.05) rotate(2deg); }
                100% { transform:scale(0.95) rotate(0deg); opacity:1; }
            }
            
            .gallery-item:hover {
                transform:scale(1.05) translateY(-5px);
                border-color:rgba(255,180,255,0.3);
                box-shadow:0 10px 40px rgba(214,106,255,0.3), inset 0 0 30px rgba(214,106,255,0.05);
                z-index:5;
            }
            
            .gallery-item img {
                width:100%;
                height:100%;
                object-fit:cover;
                user-select:none;
                -webkit-user-drag:none;
                display:block;
                transition:transform 0.6s ease;
                cursor:pointer;
            }
            .gallery-item:hover img {
                transform:scale(1.08);
            }
            
            .gallery-item .img-index {
                position:absolute;
                bottom:8px;
                right:10px;
                background:rgba(0,0,0,0.6);
                backdrop-filter:blur(10px);
                color:#fff;
                font-size:0.6rem;
                padding:2px 10px;
                border-radius:12px;
                border:1px solid rgba(255,255,255,0.05);
                font-weight:600;
                letter-spacing:0.5px;
                pointer-events:none;
            }
            
            .gallery-item .img-overlay {
                position:absolute;
                top:0;
                left:0;
                right:0;
                bottom:0;
                background:linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 60%);
                opacity:0;
                transition:0.4s;
                display:flex;
                align-items:flex-end;
                justify-content:center;
                padding:12px;
                pointer-events:none;
            }
            .gallery-item:hover .img-overlay {
                opacity:1;
            }
            .gallery-item .img-overlay span {
                color:#fff;
                font-size:0.7rem;
                font-weight:600;
                background:rgba(214,106,255,0.3);
                padding:4px 14px;
                border-radius:20px;
                backdrop-filter:blur(10px);
                border:1px solid rgba(255,255,255,0.1);
                transform:translateY(10px);
                transition:0.4s;
            }
            .gallery-item:hover .img-overlay span {
                transform:translateY(0);
            }
            
            .gallery-item .click-hint {
                position:absolute;
                top:50%;
                left:50%;
                transform:translate(-50%, -50%) scale(0.8);
                color:#fff;
                font-size:1.8rem;
                opacity:0;
                transition:0.4s;
                text-shadow:0 0 30px rgba(214,106,255,0.5);
                background:rgba(0,0,0,0.3);
                width:50px;
                height:50px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                backdrop-filter:blur(5px);
                border:1px solid rgba(255,255,255,0.1);
                pointer-events:none;
            }
            .gallery-item:hover .click-hint {
                opacity:0.8;
                transform:translate(-50%, -50%) scale(1);
            }
            
            .gallery-nav-buttons {
                display:flex;
                justify-content:center;
                gap:8px;
                padding:8px 0 2px;
            }
            .gallery-nav-buttons .gnav {
                background:rgba(255,255,255,0.04);
                border:1px solid rgba(255,180,255,0.06);
                color:#b397c4;
                width:32px;
                height:32px;
                border-radius:50%;
                cursor:pointer;
                font-size:0.7rem;
                transition:all 0.3s;
                display:inline-flex;
                align-items:center;
                justify-content:center;
            }
            .gallery-nav-buttons .gnav:hover {
                background:rgba(255,255,255,0.1);
                color:#fff;
                transform:scale(1.1);
                border-color:rgba(255,180,255,0.15);
            }
            <?php else: ?>
            .gallery-section { display:none !important; }
            <?php endif; ?>
            
            .question-box {
                background:rgba(255,220,255,0.02);
                padding:1rem 0.8rem;
                border-radius:20px;
                border:1px solid rgba(255,150,255,0.05);
                margin:0.6rem 0 1rem;
                min-height:100px;
                display:flex;
                flex-direction:column;
                justify-content:center;
                align-items:center;
                transition:all 0.4s ease;
                position:relative;
                overflow:hidden;
            }
            .question-box::before {
                content:'';
                position:absolute;
                top:-50%;
                left:-50%;
                width:200%;
                height:200%;
                background:radial-gradient(circle at 30% 50%, rgba(214,106,255,0.03) 0%, transparent 60%);
                pointer-events:none;
            }
            .question-text {
                font-size:1.05rem;
                font-weight:700;
                color:#f5d9ff;
                line-height:1.5;
                position:relative;
                z-index:1;
            }
            .question-text i { color:<?php echo $secondary; ?>; margin:0 4px; font-size:0.85rem; }
            .question-sub {
                margin-top:6px;
                font-size:0.75rem;
                color:#b397c4;
                font-weight:400;
                padding:0.2rem 0.8rem;
                border-radius:20px;
                background:rgba(255,200,255,0.02);
                border:1px solid rgba(255,180,255,0.04);
                position:relative;
                z-index:1;
            }
            .btn-group {
                display:flex;
                flex-wrap:wrap;
                align-items:center;
                justify-content:center;
                gap:8px;
                margin:0.8rem 0 0.3rem;
            }
            .btn {
                padding:0.6rem 1.2rem;
                border:none;
                border-radius:50px;
                font-family:'Nunito',sans-serif;
                font-weight:700;
                font-size:0.85rem;
                cursor:pointer;
                transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                display:inline-flex;
                align-items:center;
                gap:6px;
                flex:1;
                justify-content:center;
                min-width:100px;
                position:relative;
                overflow:hidden;
            }
            .btn i { font-size:0.9rem; }
            .btn::after {
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
            .btn:hover::after { opacity:1; }
            .btn-primary {
                background:linear-gradient(145deg, <?php echo $primary; ?>, <?php echo $secondary; ?>);
                border:1px solid rgba(255,200,255,0.10);
                color:#fff;
                box-shadow:0 4px 20px rgba(200,80,255,0.15);
            }
            .btn-primary:hover { transform:translateY(-3px) scale(1.02); box-shadow:0 8px 35px rgba(200,80,255,0.3); }
            .btn-primary:active { transform:scale(0.95); }
            .btn-secondary {
                background:rgba(255,255,255,0.02);
                border:1px solid rgba(255,150,255,0.08);
                color:#d9b3ff;
                cursor:not-allowed;
                opacity:0.35;
            }
            .hint-text {
                margin-top:0.6rem;
                font-size:0.65rem;
                color:#6a5080;
                letter-spacing:0.5px;
            }
            .hint-text i { margin:0 3px; color:#9b7aaa; }
            
            .subtle-hearts {
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                pointer-events:none;
                z-index:1;
                overflow:hidden;
            }
            .float-heart {
                position:absolute;
                font-size:0.9rem;
                opacity:0.04;
                animation:floatUp linear infinite;
                pointer-events:none;
            }
            @keyframes floatUp {
                0% { transform:translateY(100vh) scale(0.5) rotate(0deg); opacity:0; }
                10% { opacity:0.04; }
                90% { opacity:0.04; }
                100% { transform:translateY(-10vh) scale(1) rotate(360deg); opacity:0; }
            }
            
            .progress-bar {
                width:100%;
                height:3px;
                background:rgba(255,180,255,0.06);
                border-radius:10px;
                margin:0.5rem 0 0.2rem;
                overflow:hidden;
            }
            .progress-fill {
                height:100%;
                background:linear-gradient(90deg, <?php echo $primary; ?>, <?php echo $secondary; ?>);
                border-radius:10px;
                transition:width 0.4s ease;
                width:0%;
            }
            .btn:disabled { opacity:0.35; cursor:default; transform:none !important; }
            .btn:disabled::after { display:none; }
            
            @keyframes pop { 0%{transform:scale(0.85);opacity:0;} 50%{transform:scale(1.03);} 100%{transform:scale(1);opacity:1;} }
            .pop-in { animation:pop 0.35s ease forwards; }
            
            .celebrate {
                animation:celebrate 0.6s ease forwards;
            }
            @keyframes celebrate {
                0% { transform:scale(0.5) rotate(-8deg); opacity:0; }
                50% { transform:scale(1.1) rotate(4deg); }
                100% { transform:scale(1) rotate(0deg); opacity:1; }
            }
            
            /* Final Celebration */
            .emoji-rain {
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                pointer-events:none;
                z-index:100;
                overflow:hidden;
            }
            .emoji-drop {
                position:absolute;
                font-size:1.8rem;
                animation:emojiFall linear forwards;
                opacity:0;
            }
            @keyframes emojiFall {
                0% { transform:translateY(-10vh) rotate(0deg) scale(0.5); opacity:0; }
                10% { opacity:1; transform:translateY(0) rotate(20deg) scale(1); }
                90% { opacity:1; }
                100% { transform:translateY(110vh) rotate(720deg) scale(0.5); opacity:0; }
            }
            .heart-burst {
                position:fixed;
                pointer-events:none;
                z-index:99;
                font-size:2.5rem;
                animation:heartBurst 2s ease forwards;
            }
            @keyframes heartBurst {
                0% { transform:translate(0,0) scale(0.3) rotate(0deg); opacity:0; }
                20% { opacity:1; transform:scale(1.3) rotate(20deg); }
                100% { transform:translate(var(--tx), var(--ty)) scale(0.5) rotate(360deg); opacity:0; }
            }
            .confetti-piece {
                position:fixed;
                pointer-events:none;
                z-index:98;
                animation:confettiFall linear forwards;
            }
            @keyframes confettiFall {
                0% { transform:translateY(-10vh) rotate(0deg) scale(1); opacity:1; }
                100% { transform:translateY(110vh) rotate(720deg) scale(0.5); opacity:0; }
            }
            @keyframes sparkleFly {
                0% { transform:translate(0,0) scale(1); opacity:1; }
                100% { transform:translate(var(--tx), var(--ty)) scale(0); opacity:0; }
            }
            
            /* Fullscreen Image Viewer */
            .fullscreen-overlay {
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                background:rgba(0,0,0,0.92);
                backdrop-filter:blur(20px);
                -webkit-backdrop-filter:blur(20px);
                z-index:1000;
                display:flex;
                align-items:center;
                justify-content:center;
                opacity:0;
                pointer-events:none;
                transition:all 0.4s ease;
                cursor:pointer;
            }
            .fullscreen-overlay.active {
                opacity:1;
                pointer-events:all;
            }
            .fullscreen-overlay img {
                max-width:90%;
                max-height:90%;
                object-fit:contain;
                border-radius:12px;
                box-shadow:0 20px 80px rgba(0,0,0,0.8);
                transform:scale(0.9);
                transition:transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border:1px solid rgba(255,255,255,0.05);
            }
            .fullscreen-overlay.active img {
                transform:scale(1);
            }
            .fullscreen-overlay .close-btn {
                position:absolute;
                top:20px;
                right:30px;
                color:#fff;
                font-size:2rem;
                cursor:pointer;
                background:rgba(255,255,255,0.05);
                width:50px;
                height:50px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                border:1px solid rgba(255,255,255,0.05);
                transition:0.3s;
                z-index:1001;
            }
            .fullscreen-overlay .close-btn:hover {
                background:rgba(255,255,255,0.1);
                transform:rotate(90deg);
            }
            .fullscreen-overlay .img-counter {
                position:absolute;
                bottom:30px;
                left:50%;
                transform:translateX(-50%);
                color:#b397c4;
                font-size:0.85rem;
                background:rgba(0,0,0,0.4);
                padding:6px 20px;
                border-radius:20px;
                border:1px solid rgba(255,255,255,0.05);
                z-index:1001;
            }
            .fullscreen-overlay .nav-btn {
                position:absolute;
                top:50%;
                transform:translateY(-50%);
                background:rgba(255,255,255,0.05);
                border:1px solid rgba(255,255,255,0.05);
                color:#fff;
                width:50px;
                height:50px;
                border-radius:50%;
                cursor:pointer;
                font-size:1.2rem;
                transition:0.3s;
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:1001;
            }
            .fullscreen-overlay .nav-btn:hover {
                background:rgba(255,255,255,0.1);
            }
            .fullscreen-overlay .nav-btn.prev { left:20px; }
            .fullscreen-overlay .nav-btn.next { right:20px; }
            
            @media (max-width:480px) {
                .fullscreen-overlay .nav-btn { width:40px; height:40px; font-size:1rem; }
                .fullscreen-overlay .nav-btn.prev { left:10px; }
                .fullscreen-overlay .nav-btn.next { right:10px; }
                .fullscreen-overlay .close-btn { top:15px; right:15px; width:40px; height:40px; font-size:1.5rem; }
                .gallery-item { min-width:90px; max-width:110px; }
                .gallery-scroll { gap:10px; padding:4px 10px; }
                .proposal-card { padding:1.2rem 0.8rem; border-radius:22px; max-height:98vh; }
                .morgan-name { font-size:2rem; }
                .morgan-name i { font-size:1.4rem; }
                .question-text { font-size:0.95rem; }
                .btn { font-size:0.75rem; padding:0.5rem 0.8rem; min-width:80px; }
                .question-box { padding:0.8rem 0.6rem; min-height:80px; }
                .gallery-item .click-hint { width:35px; height:35px; font-size:1.2rem; }
            }
            @media (max-width:360px) {
                .gallery-item { min-width:70px; max-width:85px; }
                .gallery-scroll { gap:8px; padding:4px 8px; }
                .morgan-name { font-size:1.6rem; }
                .morgan-name i { font-size:1.2rem; }
                .question-text { font-size:0.85rem; }
                .btn { font-size:0.7rem; padding:0.4rem 0.6rem; min-width:65px; gap:4px; }
                .btn i { font-size:0.7rem; }
                .proposal-card { padding:0.8rem 0.6rem; }
                .gallery-item .click-hint { width:30px; height:30px; font-size:1rem; }
            }
        </style>
    </head>
    <body>
        <?php if ($enableHearts): ?>
        <div class="subtle-hearts" id="subtleHearts"></div>
        <?php endif; ?>
        <div class="orb orb1"></div>
        <div class="orb orb2"></div>
        
        <!-- Fullscreen Image Viewer -->
        <div class="fullscreen-overlay" id="fullscreenOverlay">
            <button class="close-btn" onclick="closeFullscreen()"><i class="fas fa-times"></i></button>
            <button class="nav-btn prev" onclick="event.stopPropagation(); navigateFullscreen(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next" onclick="event.stopPropagation(); navigateFullscreen(1)"><i class="fas fa-chevron-right"></i></button>
            <img id="fullscreenImg" src="" alt="Fullscreen view">
            <div class="img-counter" id="fullscreenCounter">1 / 1</div>
        </div>
        
        <div class="proposal-card" id="proposalCard">
            <div class="morgan-name">
                <i class="fas fa-crown"></i> <?php echo htmlspecialchars($name); ?> <i class="fas fa-heart"></i>
            </div>
            <div class="sub-head">✨ Love • Mazaak • Romance ✨</div>
            
            <?php if ($hasGallery): ?>
            <div class="gallery-section" id="gallerySection">
                <div class="gallery-scroll" id="galleryScroll">
                    <?php foreach ($gallery as $index => $img): ?>
                    <div class="gallery-item" data-index="<?php echo $index; ?>" onclick="openFullscreen(<?php echo $index; ?>)">
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Image <?php echo $index+1; ?>" loading="lazy" draggable="false">
                        <div class="img-overlay"><span><i class="fas fa-expand"></i> View</span></div>
                        <div class="click-hint"><i class="fas fa-search-plus"></i></div>
                        <span class="img-index"><?php echo $index+1; ?>/<?php echo count($gallery); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($gallery) > 1): ?>
                <div class="gallery-nav-buttons">
                    <button class="gnav" onclick="scrollGallery(-1)"><i class="fas fa-chevron-left"></i></button>
                    <button class="gnav" onclick="scrollGallery(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="question-box" id="questionBox">
                <div class="question-text" id="questionText">
                    <i class="fas fa-question-circle"></i> 
                    <?php echo htmlspecialchars($questions[0] ?? 'Kya tum meri "permanent seat" banogi?'); ?>
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="question-sub" id="questionSub">
                    <i class="fas fa-grin-hearts"></i> (sahi jawab "haan" hai, pata hai na?) <i class="fas fa-grin-hearts"></i>
                </div>
            </div>
            
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            
            <div class="btn-group">
                <button class="btn btn-primary" id="yesBtn">
                    <i class="fas fa-thumbs-up"></i> Haan, bilkul!
                </button>
                <button class="btn btn-secondary" id="noBtn">
                    <i class="fas fa-times-circle"></i> Naa
                </button>
            </div>
            <div class="hint-text">
                <i class="fas fa-arrow-right"></i> Sirf "Haan" dabao, aage badhte raho <i class="fas fa-arrow-left"></i>
            </div>
        </div>
        
        <script>
            // ============================================
            // FULLSCREEN IMAGE VIEWER - FIXED
            // ============================================
            var galleryImages = <?php echo json_encode($gallery); ?>;
            var currentFullscreenIndex = 0;
            var isFullscreenOpen = false;
            
            function openFullscreen(index) {
                if (typeof index !== 'number') {
                    index = 0;
                }
                if (!galleryImages.length || index >= galleryImages.length) return;
                
                currentFullscreenIndex = index;
                var overlay = document.getElementById('fullscreenOverlay');
                var img = document.getElementById('fullscreenImg');
                var counter = document.getElementById('fullscreenCounter');
                
                if (galleryImages[currentFullscreenIndex]) {
                    img.src = galleryImages[currentFullscreenIndex];
                    counter.textContent = (currentFullscreenIndex + 1) + ' / ' + galleryImages.length;
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                    isFullscreenOpen = true;
                }
            }
            
            function closeFullscreen() {
                var overlay = document.getElementById('fullscreenOverlay');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                isFullscreenOpen = false;
            }
            
            function navigateFullscreen(direction) {
                if (event) event.stopPropagation();
                currentFullscreenIndex += direction;
                if (currentFullscreenIndex < 0) currentFullscreenIndex = galleryImages.length - 1;
                if (currentFullscreenIndex >= galleryImages.length) currentFullscreenIndex = 0;
                
                var img = document.getElementById('fullscreenImg');
                var counter = document.getElementById('fullscreenCounter');
                
                if (galleryImages[currentFullscreenIndex]) {
                    img.style.transform = 'scale(0.8)';
                    setTimeout(function() {
                        img.src = galleryImages[currentFullscreenIndex];
                        counter.textContent = (currentFullscreenIndex + 1) + ' / ' + galleryImages.length;
                        setTimeout(function() {
                            img.style.transform = 'scale(1)';
                        }, 50);
                    }, 150);
                }
            }
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (!isFullscreenOpen) return;
                if (e.key === 'Escape') closeFullscreen();
                if (e.key === 'ArrowLeft') navigateFullscreen(-1);
                if (e.key === 'ArrowRight') navigateFullscreen(1);
            });
            
            // Close on overlay click
            document.getElementById('fullscreenOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeFullscreen();
                }
            });
            
            // ============================================
            // GALLERY AUTO SCROLL
            // ============================================
            (function() {
                var scrollEl = document.getElementById('galleryScroll');
                if (!scrollEl) return;
                
                var items = scrollEl.querySelectorAll('.gallery-item');
                if (items.length <= 1) return;
                
                var currentIndex = 0;
                var autoScrollInterval = null;
                
                function scrollToIndex(index) {
                    if (index >= items.length) index = 0;
                    if (index < 0) index = items.length - 1;
                    currentIndex = index;
                    var item = items[currentIndex];
                    if (item) {
                        item.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    }
                }
                
                function nextSlide() {
                    scrollToIndex(currentIndex + 1);
                }
                
                function startAutoScroll() {
                    if (autoScrollInterval) clearInterval(autoScrollInterval);
                    autoScrollInterval = setInterval(nextSlide, 3500);
                }
                
                function stopAutoScroll() {
                    if (autoScrollInterval) {
                        clearInterval(autoScrollInterval);
                        autoScrollInterval = null;
                    }
                }
                
                startAutoScroll();
                
                scrollEl.addEventListener('mouseenter', stopAutoScroll);
                scrollEl.addEventListener('mouseleave', startAutoScroll);
                scrollEl.addEventListener('touchstart', stopAutoScroll, { passive: true });
                scrollEl.addEventListener('touchend', startAutoScroll, { passive: true });
                
                var isDown = false;
                var startX = 0;
                var scrollLeft = 0;
                
                scrollEl.addEventListener('mousedown', function(e) {
                    isDown = true;
                    startX = e.pageX - scrollEl.offsetLeft;
                    scrollLeft = scrollEl.scrollLeft;
                    scrollEl.style.cursor = 'grabbing';
                    stopAutoScroll();
                });
                
                scrollEl.addEventListener('mouseleave', function() {
                    if (isDown) {
                        isDown = false;
                        scrollEl.style.cursor = 'grab';
                        setTimeout(startAutoScroll, 3000);
                    }
                });
                
                scrollEl.addEventListener('mouseup', function() {
                    if (isDown) {
                        isDown = false;
                        scrollEl.style.cursor = 'grab';
                        setTimeout(startAutoScroll, 3000);
                    }
                });
                
                scrollEl.addEventListener('mousemove', function(e) {
                    if (!isDown) return;
                    e.preventDefault();
                    var x = e.pageX - scrollEl.offsetLeft;
                    var walk = (x - startX) * 2;
                    scrollEl.scrollLeft = scrollLeft - walk;
                });
                
                var touchStartX = 0;
                var touchScrollLeft = 0;
                
                scrollEl.addEventListener('touchstart', function(e) {
                    touchStartX = e.touches[0].pageX - scrollEl.offsetLeft;
                    touchScrollLeft = scrollEl.scrollLeft;
                    stopAutoScroll();
                }, { passive: true });
                
                scrollEl.addEventListener('touchmove', function(e) {
                    var x = e.touches[0].pageX - scrollEl.offsetLeft;
                    var walk = (x - touchStartX) * 2;
                    scrollEl.scrollLeft = touchScrollLeft - walk;
                }, { passive: true });
                
                scrollEl.addEventListener('touchend', function() {
                    setTimeout(startAutoScroll, 3000);
                }, { passive: true });
            })();
            
            window.scrollGallery = function(direction) {
                var scrollEl = document.getElementById('galleryScroll');
                if (!scrollEl) return;
                var scrollAmount = 160;
                scrollEl.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
            };
            
            // ============================================
            // LOVE PROPOSAL LOGIC
            // ============================================
            (function() {
                var subtleContainer = document.getElementById('subtleHearts');
                var hearts = ['♥', '♡', '❤', '💕', '💗'];
                var enableHearts = <?php echo $enableHearts ? 'true' : 'false'; ?>;
                
                if (enableHearts && subtleContainer) {
                    function createSubtleHeart() {
                        var el = document.createElement('div');
                        el.className = 'float-heart';
                        el.textContent = hearts[Math.floor(Math.random() * hearts.length)];
                        el.style.left = Math.random() * 100 + '%';
                        el.style.fontSize = (0.5 + Math.random() * 0.9) + 'rem';
                        el.style.animationDuration = (12 + Math.random() * 16) + 's';
                        el.style.animationDelay = Math.random() * 10 + 's';
                        el.style.opacity = 0.03 + Math.random() * 0.05;
                        el.style.color = 'hsl(' + (320 + Math.random() * 40) + ', 80%, 70%)';
                        subtleContainer.appendChild(el);
                        setTimeout(function() { if (el.parentNode) el.remove(); }, 30000);
                    }
                    for (var i = 0; i < 8; i++) setTimeout(createSubtleHeart, i * 200);
                    setInterval(createSubtleHeart, 800);
                }
                
                var questions = <?php echo json_encode($questions); ?>;
                var finalMessage = <?php echo json_encode($finalMessage); ?>;
                var primaryColor = <?php echo json_encode($primary); ?>;
                
                var currentStep = 0;
                var totalSteps = questions.length;
                var questionEl = document.getElementById('questionText');
                var subEl = document.getElementById('questionSub');
                var questionBox = document.getElementById('questionBox');
                var progressFill = document.getElementById('progressFill');
                var yesBtn = document.getElementById('yesBtn');
                var noBtn = document.getElementById('noBtn');
                var proposalCard = document.getElementById('proposalCard');
                
                function updateProgress() {
                    var progress = ((currentStep) / totalSteps) * 100;
                    progressFill.style.width = progress + '%';
                }
                
                function triggerFinalCelebration() {
                    var emojis = ['❤️', '💖', '💗', '💘', '💝', '✨', '⭐', '🎉', '🥳', '💕', '♥️', '💓', '💞', '💟', '❣️'];
                    var colors = ['#ff4d8f', '#d66aff', '#ff77b0', '#ffb84d', '#4dc9ff', '#ff6b6b', '#ffd93d', '#6bcb77', '#ff77b0', '#d9b3ff'];
                    
                    for (var i = 0; i < 50; i++) {
                        (function(i) {
                            setTimeout(function() {
                                var emoji = document.createElement('div');
                                emoji.className = 'emoji-drop';
                                emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
                                emoji.style.left = (2 + Math.random() * 96) + '%';
                                emoji.style.fontSize = (1.2 + Math.random() * 2.5) + 'rem';
                                emoji.style.animationDuration = (2.5 + Math.random() * 3) + 's';
                                emoji.style.animationDelay = (Math.random() * 1.5) + 's';
                                emoji.style.color = colors[Math.floor(Math.random() * colors.length)];
                                document.body.appendChild(emoji);
                                setTimeout(function() { if (emoji.parentNode) emoji.remove(); }, 5000);
                            }, i * 40);
                        })(i);
                    }
                    
                    for (var i = 0; i < 25; i++) {
                        (function(i) {
                            setTimeout(function() {
                                var heart = document.createElement('div');
                                heart.className = 'heart-burst';
                                heart.textContent = ['❤️', '💖', '💗', '💘', '💝'][Math.floor(Math.random() * 5)];
                                heart.style.left = (10 + Math.random() * 80) + '%';
                                heart.style.top = (10 + Math.random() * 80) + '%';
                                heart.style.fontSize = (1.8 + Math.random() * 3) + 'rem';
                                heart.style.color = colors[Math.floor(Math.random() * colors.length)];
                                heart.style.textShadow = '0 0 30px ' + colors[Math.floor(Math.random() * colors.length)];
                                var angle = Math.random() * 2 * Math.PI;
                                var distance = 100 + Math.random() * 180;
                                heart.style.setProperty('--tx', Math.cos(angle) * distance + 'px');
                                heart.style.setProperty('--ty', Math.sin(angle) * distance + 'px');
                                document.body.appendChild(heart);
                                setTimeout(function() { if (heart.parentNode) heart.remove(); }, 2500);
                            }, i * 60);
                        })(i);
                    }
                    
                    for (var i = 0; i < 40; i++) {
                        (function(i) {
                            setTimeout(function() {
                                var confetti = document.createElement('div');
                                confetti.className = 'confetti-piece';
                                confetti.style.left = (2 + Math.random() * 96) + '%';
                                confetti.style.top = '-5%';
                                confetti.style.width = (6 + Math.random() * 10) + 'px';
                                confetti.style.height = (6 + Math.random() * 10) + 'px';
                                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                                confetti.style.animationDuration = (2.5 + Math.random() * 3) + 's';
                                confetti.style.animationDelay = (Math.random() * 1.5) + 's';
                                confetti.style.boxShadow = '0 0 15px ' + colors[Math.floor(Math.random() * colors.length)];
                                document.body.appendChild(confetti);
                                setTimeout(function() { if (confetti.parentNode) confetti.remove(); }, 5000);
                            }, i * 50);
                        })(i);
                    }
                    
                    for (var i = 0; i < 30; i++) {
                        (function(i) {
                            setTimeout(function() {
                                var spark = document.createElement('div');
                                var size = 4 + Math.random() * 8;
                                spark.style.cssText = 'position:fixed; pointer-events:none; z-index:99; width:' + size + 'px; height:' + size + 'px; border-radius:50%; background:hsl(' + (Math.random() * 360) + ', 100%, 70%); box-shadow:0 0 20px hsl(' + (Math.random() * 360) + ', 100%, 70%); left:' + (10 + Math.random() * 80) + '%; top:' + (10 + Math.random() * 80) + '%; animation:sparkleFly 1.5s ease forwards;';
                                var angle = Math.random() * 2 * Math.PI;
                                var distance = 80 + Math.random() * 150;
                                spark.style.setProperty('--tx', Math.cos(angle) * distance + 'px');
                                spark.style.setProperty('--ty', Math.sin(angle) * distance + 'px');
                                document.body.appendChild(spark);
                                setTimeout(function() { if (spark.parentNode) spark.remove(); }, 2000);
                            }, i * 30);
                        })(i);
                    }
                }
                
                function loadQuestion(index) {
                    if (index >= totalSteps) {
                        questionEl.innerHTML = '<i class="fas fa-heart" style="color: #ff66aa; font-size: 2.2rem; display:block; margin-bottom:6px; animation:pulse 1s infinite;"></i><span style="display: block; font-size: 2.4rem; font-weight: 800; color: #ffd9f2; margin: 6px 0; text-shadow: 0 0 30px rgba(255,105,180,0.3);">' + finalMessage + '</span><span style="display: block; font-size: 1.1rem; color: #e0c8f0; margin-top:4px;"><i class="fas fa-star" style="color:' + primaryColor + ';"></i> You are my forever! <i class="fas fa-star" style="color:' + primaryColor + ';"></i></span><span style="display: block; font-size: 2rem; margin-top:6px; animation:emojiBounce 1.5s infinite;">💕 💖 💗 💘 💝 ✨</span>';
                        subEl.innerHTML = '<i class="fas fa-heart" style="color:#ff66aa;"></i> (ab roz pizza aur pyaar milega 🍕❤️) <i class="fas fa-heart" style="color:#ff66aa;"></i>';
                        
                        yesBtn.disabled = true;
                        noBtn.disabled = true;
                        yesBtn.style.opacity = '0.35';
                        noBtn.style.opacity = '0.2';
                        yesBtn.innerHTML = '<i class="fas fa-check-circle"></i> Done!';
                        noBtn.innerHTML = '<i class="fas fa-smile"></i> Pehle hi haan!';
                        progressFill.style.width = '100%';
                        
                        questionBox.classList.add('celebrate');
                        proposalCard.classList.add('celebrate');
                        
                        setTimeout(triggerFinalCelebration, 300);
                        return true;
                    }
                    
                    var item = questions[index];
                    questionEl.innerHTML = '<i class="fas fa-question-circle"></i> ' + item + ' <i class="fas fa-question-circle"></i>';
                    subEl.innerHTML = '<i class="fas fa-grin-hearts"></i> (sahi jawab "haan" hai) <i class="fas fa-grin-hearts"></i>';
                    questionBox.classList.remove('pop-in');
                    void questionBox.offsetWidth;
                    questionBox.classList.add('pop-in');
                    updateProgress();
                    return false;
                }
                
                loadQuestion(0);
                
                yesBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (yesBtn.disabled) return;
                    currentStep++;
                    var finished = loadQuestion(currentStep);
                    if (!finished && enableHearts && subtleContainer) {
                        for (var i = 0; i < 6; i++) {
                            (function(i) {
                                setTimeout(function() {
                                    var h = document.createElement('div');
                                    h.className = 'float-heart';
                                    h.textContent = ['❤', '♥', '💕', '💗'][Math.floor(Math.random() * 4)];
                                    h.style.left = (20 + Math.random() * 60) + '%';
                                    h.style.top = (20 + Math.random() * 60) + '%';
                                    h.style.fontSize = (0.8 + Math.random() * 1.5) + 'rem';
                                    h.style.animationDuration = (2 + Math.random() * 2) + 's';
                                    h.style.animationDelay = '0s';
                                    h.style.opacity = '0.3';
                                    h.style.color = 'hsl(' + (320 + Math.random() * 40) + ', 90%, 70%)';
                                    subtleContainer.appendChild(h);
                                    setTimeout(function() { if (h.parentNode) h.remove(); }, 2800);
                                }, i * 40);
                            })(i);
                        }
                    }
                });
                
                noBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    noBtn.style.transform = 'scale(0.92) rotate(-3deg)';
                    setTimeout(function() { noBtn.style.transform = 'scale(1) rotate(0deg)'; }, 250);
                    var sub = document.getElementById('questionSub');
                    sub.innerHTML = '<i class="fas fa-grin-wink"></i> (naa nahi chalega, haan hi karna padega 😜) <i class="fas fa-grin-wink"></i>';
                    setTimeout(function() {
                        if (currentStep < totalSteps) {
                            sub.innerHTML = '<i class="fas fa-grin-hearts"></i> (sahi jawab "haan" hai) <i class="fas fa-grin-hearts"></i>';
                        }
                    }, 1800);
                });
                
                updateProgress();
            })();
        </script>
    </body>
    </html>
    <?php
    exit;
}

// ============================================
// BIRTHDAY PAGE
// ============================================
if ($type === 'birthday') {
    $name = $settings['name'] ?? 'Morgan';
    $age = $settings['age'] ?? 19;
    $wishes = $settings['wishes'] ?? ['🎂 Happy Birthday!'];
    $finalMessage = $settings['finalMessage'] ?? 'Best birthday ever! 🎉';
    $primary = $settings['colors']['primary'] ?? '#ff77b0';
    $secondary = $settings['colors']['secondary'] ?? '#d9b3ff';
    $bg = $settings['colors']['bg'] ?? 'linear-gradient(135deg, #1a0b1f, #2d1340)';
    $enableHearts = $settings['enableHearts'] ?? true;
    $gallery = $settings['gallery'] ?? [];
    $hasGallery = !empty($gallery);
    ?>
    <!DOCTYPE html>
    <html lang="hi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
        <title>🎂 Happy Birthday <?php echo htmlspecialchars($name); ?>!</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            * { margin:0; padding:0; box-sizing:border-box; }
            html, body {
                min-height:100vh;
                background: <?php echo $bg; ?>;
                display:flex;
                align-items:center;
                justify-content:center;
                font-family:'Nunito',sans-serif;
                padding:10px;
                position:relative;
                overflow-x:hidden;
            }
            .circle {
                position:fixed;
                border-radius:50%;
                pointer-events:none;
                z-index:0;
                animation:float 20s infinite alternate ease-in-out;
            }
            .circle1 { width:200px; height:200px; background:rgba(255,77,143,0.06); top:-80px; right:-80px; }
            .circle2 { width:280px; height:280px; background:rgba(155,77,255,0.06); bottom:-100px; left:-100px; animation-delay:5s; }
            @keyframes float { 0%{transform:translate(0,0) scale(1);} 100%{transform:translate(20px,-20px) scale(1.08);} }
            
            .birthday-card {
                position:relative;
                z-index:10;
                max-width:420px;
                width:100%;
                background:rgba(20,10,28,0.88);
                backdrop-filter:blur(30px);
                -webkit-backdrop-filter:blur(30px);
                border-radius:28px;
                padding:1.6rem 1.2rem;
                text-align:center;
                border:1px solid rgba(255,180,255,0.06);
                box-shadow:0 25px 80px rgba(0,0,0,0.5);
                animation:fadeScale 0.6s ease;
                max-height:95vh;
                overflow-y:auto;
            }
            .birthday-card::-webkit-scrollbar { width:3px; }
            .birthday-card::-webkit-scrollbar-track { background:transparent; }
            .birthday-card::-webkit-scrollbar-thumb { background:rgba(255,180,255,0.15); border-radius:10px; }
            @keyframes fadeScale { 0%{opacity:0;transform:scale(0.95);} 100%{opacity:1;transform:scale(1);} }
            
            .cute-decoration { font-size:2rem; margin-bottom:6px; letter-spacing:8px; animation:bounce 2s infinite; }
            @keyframes bounce { 0%,100%{transform:scale(1);} 50%{transform:scale(1.08);} }
            .name {
                font-family:'Playfair Display',serif;
                font-weight:700;
                font-size:2.6rem;
                background:linear-gradient(135deg, <?php echo $secondary; ?>, <?php echo $primary; ?>, <?php echo $secondary; ?>);
                -webkit-background-clip:text;
                background-clip:text;
                color:transparent;
                line-height:1.1;
                margin-bottom:2px;
                letter-spacing:1px;
                text-shadow:0 0 40px rgba(255,119,176,0.1);
            }
            .name i { font-size:2rem; margin:0 4px; background:linear-gradient(135deg, <?php echo $primary; ?>, <?php echo $secondary; ?>); -webkit-background-clip:text; background-clip:text; color:transparent; }
            .badge {
                display:inline-block;
                background:rgba(255,77,143,0.10);
                padding:0.15rem 1.2rem;
                border-radius:50px;
                color:<?php echo $primary; ?>;
                font-weight:600;
                font-size:0.7rem;
                letter-spacing:2px;
                border:1px solid rgba(255,77,143,0.06);
                margin-bottom:0.6rem;
            }
            .badge i { margin:0 4px; }
            .age-display {
                display:inline-block;
                background:rgba(255,77,143,0.06);
                padding:0.1rem 1rem;
                border-radius:50px;
                color:<?php echo $primary; ?>;
                font-weight:700;
                font-size:0.9rem;
                border:1px solid rgba(255,77,143,0.06);
                margin-bottom:0.6rem;
            }
            .age-display i { margin:0 4px; }
            
            <?php if ($hasGallery): ?>
            .gallery-section {
                margin:0.6rem 0 1rem;
                border-radius:16px;
                background:rgba(0,0,0,0.25);
                border:1px solid rgba(255,180,255,0.06);
                overflow:hidden;
                position:relative;
                padding:8px 0;
                box-shadow:inset 0 0 30px rgba(0,0,0,0.2);
            }
            .gallery-scroll {
                display:flex;
                overflow-x:auto;
                scroll-behavior:smooth;
                gap:14px;
                padding:6px 14px;
                scroll-snap-type:x mandatory;
                -webkit-overflow-scrolling:touch;
                cursor:grab;
                scrollbar-width:thin;
                padding-bottom:10px;
            }
            .gallery-scroll::-webkit-scrollbar { height:4px; }
            .gallery-scroll::-webkit-scrollbar-track { background:rgba(255,255,255,0.03); border-radius:10px; }
            .gallery-scroll::-webkit-scrollbar-thumb { background:linear-gradient(90deg, <?php echo $primary; ?>, <?php echo $secondary; ?>); border-radius:10px; }
            .gallery-scroll:active { cursor:grabbing; }
            
            .gallery-item {
                min-width:120px;
                max-width:150px;
                flex-shrink:0;
                aspect-ratio:1;
                border-radius:16px;
                overflow:hidden;
                scroll-snap-align:center;
                border:2px solid rgba(255,180,255,0.06);
                transition:all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                background:rgba(0,0,0,0.3);
                position:relative;
                transform:scale(0.95);
                animation:imgAppear 0.6s ease forwards;
                cursor:pointer;
                box-shadow:0 4px 15px rgba(0,0,0,0.2);
            }
            .gallery-item:nth-child(1) { animation-delay:0.1s; }
            .gallery-item:nth-child(2) { animation-delay:0.2s; }
            .gallery-item:nth-child(3) { animation-delay:0.3s; }
            .gallery-item:nth-child(4) { animation-delay:0.4s; }
            .gallery-item:nth-child(5) { animation-delay:0.5s; }
            .gallery-item:nth-child(6) { animation-delay:0.6s; }
            .gallery-item:nth-child(7) { animation-delay:0.7s; }
            .gallery-item:nth-child(8) { animation-delay:0.8s; }
            
            @keyframes imgAppear {
                0% { transform:scale(0.8) rotate(-5deg); opacity:0; }
                60% { transform:scale(1.05) rotate(2deg); }
                100% { transform:scale(0.95) rotate(0deg); opacity:1; }
            }
            
            .gallery-item:hover {
                transform:scale(1.05) translateY(-5px);
                border-color:rgba(255,180,255,0.3);
                box-shadow:0 10px 40px rgba(255,119,176,0.3), inset 0 0 30px rgba(255,119,176,0.05);
                z-index:5;
            }
            
            .gallery-item img {
                width:100%;
                height:100%;
                object-fit:cover;
                user-select:none;
                -webkit-user-drag:none;
                display:block;
                transition:transform 0.6s ease;
                cursor:pointer;
            }
            .gallery-item:hover img {
                transform:scale(1.08);
            }
            
            .gallery-item .img-index {
                position:absolute;
                bottom:8px;
                right:10px;
                background:rgba(0,0,0,0.6);
                backdrop-filter:blur(10px);
                color:#fff;
                font-size:0.6rem;
                padding:2px 10px;
                border-radius:12px;
                border:1px solid rgba(255,255,255,0.05);
                font-weight:600;
                letter-spacing:0.5px;
                pointer-events:none;
            }
            
            .gallery-item .img-overlay {
                position:absolute;
                top:0;
                left:0;
                right:0;
                bottom:0;
                background:linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 60%);
                opacity:0;
                transition:0.4s;
                display:flex;
                align-items:flex-end;
                justify-content:center;
                padding:12px;
                pointer-events:none;
            }
            .gallery-item:hover .img-overlay {
                opacity:1;
            }
            .gallery-item .img-overlay span {
                color:#fff;
                font-size:0.7rem;
                font-weight:600;
                background:rgba(255,119,176,0.3);
                padding:4px 14px;
                border-radius:20px;
                backdrop-filter:blur(10px);
                border:1px solid rgba(255,255,255,0.1);
                transform:translateY(10px);
                transition:0.4s;
            }
            .gallery-item:hover .img-overlay span {
                transform:translateY(0);
            }
            
            .gallery-item .click-hint {
                position:absolute;
                top:50%;
                left:50%;
                transform:translate(-50%, -50%) scale(0.8);
                color:#fff;
                font-size:1.8rem;
                opacity:0;
                transition:0.4s;
                text-shadow:0 0 30px rgba(255,119,176,0.5);
                background:rgba(0,0,0,0.3);
                width:50px;
                height:50px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                backdrop-filter:blur(5px);
                border:1px solid rgba(255,255,255,0.1);
                pointer-events:none;
            }
            .gallery-item:hover .click-hint {
                opacity:0.8;
                transform:translate(-50%, -50%) scale(1);
            }
            
            .gallery-nav-buttons {
                display:flex;
                justify-content:center;
                gap:8px;
                padding:8px 0 2px;
            }
            .gallery-nav-buttons .gnav {
                background:rgba(255,255,255,0.04);
                border:1px solid rgba(255,180,255,0.06);
                color:#b397c4;
                width:32px;
                height:32px;
                border-radius:50%;
                cursor:pointer;
                font-size:0.7rem;
                transition:all 0.3s;
                display:inline-flex;
                align-items:center;
                justify-content:center;
            }
            .gallery-nav-buttons .gnav:hover {
                background:rgba(255,255,255,0.1);
                color:#fff;
                transform:scale(1.1);
                border-color:rgba(255,180,255,0.15);
            }
            <?php else: ?>
            .gallery-section { display:none !important; }
            <?php endif; ?>
            
            .message-box {
                background:rgba(255,255,255,0.01);
                padding:1rem 0.8rem;
                border-radius:20px;
                border:1px solid rgba(255,180,255,0.04);
                margin:0.6rem 0 1rem;
                min-height:90px;
                display:flex;
                flex-direction:column;
                justify-content:center;
                align-items:center;
                transition:all 0.3s ease;
                position:relative;
                overflow:hidden;
            }
            .message-box::before {
                content:'';
                position:absolute;
                top:-50%;
                left:-50%;
                width:200%;
                height:200%;
                background:radial-gradient(circle at 30% 50%, rgba(255,119,176,0.03) 0%, transparent 60%);
                pointer-events:none;
            }
            .message {
                font-size:1rem;
                color:#f0d9ff;
                line-height:1.5;
                font-weight:400;
                position:relative;
                z-index:1;
            }
            .message .highlight { color:<?php echo $primary; ?>; font-weight:700; }
            .sub-message {
                margin-top:6px;
                font-size:0.8rem;
                color:#b397c4;
                font-weight:400;
                padding:0.2rem 0.8rem;
                border-radius:20px;
                background:rgba(255,200,255,0.02);
                border:1px solid rgba(255,180,255,0.04);
                position:relative;
                z-index:1;
            }
            .btn-group {
                display:flex;
                flex-wrap:wrap;
                align-items:center;
                justify-content:center;
                gap:8px;
                margin:0.3rem 0;
            }
            .btn {
                padding:0.6rem 1.2rem;
                border:none;
                border-radius:50px;
                font-family:'Nunito',sans-serif;
                font-weight:700;
                font-size:0.8rem;
                cursor:pointer;
                transition:all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                display:inline-flex;
                align-items:center;
                gap:6px;
                flex:1;
                justify-content:center;
                min-width:90px;
                position:relative;
                overflow:hidden;
            }
            .btn i { font-size:0.85rem; }
            .btn::after {
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
            .btn:hover::after { opacity:1; }
            .btn-pink {
                background:linear-gradient(135deg, <?php echo $primary; ?>, <?php echo $secondary; ?>);
                border:1px solid rgba(255,200,255,0.08);
                color:#fff;
                box-shadow:0 4px 20px rgba(200,80,255,0.12);
            }
            .btn-pink:hover { transform:translateY(-3px) scale(1.02); box-shadow:0 8px 35px rgba(200,80,255,0.2); }
            .btn-pink:active { transform:scale(0.95); }
            .btn-soft {
                background:rgba(255,255,255,0.02);
                border:1px solid rgba(255,150,255,0.06);
                color:#d9b3ff;
            }
            .btn-soft:hover { background:rgba(255,200,255,0.04); border-color:rgba(255,150,255,0.12); }
            .btn-soft:active { transform:scale(0.95); }
            .footer-text {
                margin-top:0.6rem;
                font-size:0.65rem;
                color:#6a5080;
                letter-spacing:0.5px;
            }
            .footer-text i { margin:0 4px; color:#8a6a9a; }
            .tiny-hearts {
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                pointer-events:none;
                z-index:1;
                overflow:hidden;
            }
            .tiny-heart {
                position:absolute;
                font-size:0.6rem;
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
            @keyframes pop { 0%{transform:scale(0.85);opacity:0;} 50%{transform:scale(1.03);} 100%{transform:scale(1);opacity:1;} }
            .pop-in { animation:pop 0.35s ease forwards; }
            
            .birthday-emoji-rain {
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                pointer-events:none;
                z-index:100;
                overflow:hidden;
            }
            .birthday-emoji-drop {
                position:absolute;
                font-size:1.8rem;
                animation:emojiFall linear forwards;
                opacity:0;
            }
            @keyframes emojiFall {
                0% { transform:translateY(-10vh) rotate(0deg) scale(0.5); opacity:0; }
                10% { opacity:1; transform:translateY(0) rotate(20deg) scale(1); }
                90% { opacity:1; }
                100% { transform:translateY(110vh) rotate(720deg) scale(0.5); opacity:0; }
            }
            .birthday-heart-burst {
                position:fixed;
                pointer-events:none;
                z-index:99;
                font-size:2.5rem;
                animation:heartBurst 2s ease forwards;
            }
            @keyframes heartBurst {
                0% { transform:translate(0,0) scale(0.3) rotate(0deg); opacity:0; }
                20% { opacity:1; transform:scale(1.3) rotate(20deg); }
                100% { transform:translate(var(--tx), var(--ty)) scale(0.5) rotate(360deg); opacity:0; }
            }
            .birthday-confetti {
                position:fixed;
                pointer-events:none;
                z-index:98;
                animation:confettiFall linear forwards;
            }
            @keyframes confettiFall {
                0% { transform:translateY(-10vh) rotate(0deg) scale(1); opacity:1; }
                100% { transform:translateY(110vh) rotate(720deg) scale(0.5); opacity:0; }
            }
            
            .fullscreen-overlay {
                position:fixed;
                top:0;
                left:0;
                width:100%;
                height:100%;
                background:rgba(0,0,0,0.92);
                backdrop-filter:blur(20px);
                -webkit-backdrop-filter:blur(20px);
                z-index:1000;
                display:flex;
                align-items:center;
                justify-content:center;
                opacity:0;
                pointer-events:none;
                transition:all 0.4s ease;
                cursor:pointer;
            }
            .fullscreen-overlay.active {
                opacity:1;
                pointer-events:all;
            }
            .fullscreen-overlay img {
                max-width:90%;
                max-height:90%;
                object-fit:contain;
                border-radius:12px;
                box-shadow:0 20px 80px rgba(0,0,0,0.8);
                transform:scale(0.9);
                transition:transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border:1px solid rgba(255,255,255,0.05);
            }
            .fullscreen-overlay.active img {
                transform:scale(1);
            }
            .fullscreen-overlay .close-btn {
                position:absolute;
                top:20px;
                right:30px;
                color:#fff;
                font-size:2rem;
                cursor:pointer;
                background:rgba(255,255,255,0.05);
                width:50px;
                height:50px;
                border-radius:50%;
                display:flex;
                align-items:center;
                justify-content:center;
                border:1px solid rgba(255,255,255,0.05);
                transition:0.3s;
                z-index:1001;
            }
            .fullscreen-overlay .close-btn:hover {
                background:rgba(255,255,255,0.1);
                transform:rotate(90deg);
            }
            .fullscreen-overlay .img-counter {
                position:absolute;
                bottom:30px;
                left:50%;
                transform:translateX(-50%);
                color:#b397c4;
                font-size:0.85rem;
                background:rgba(0,0,0,0.4);
                padding:6px 20px;
                border-radius:20px;
                border:1px solid rgba(255,255,255,0.05);
                z-index:1001;
            }
            .fullscreen-overlay .nav-btn {
                position:absolute;
                top:50%;
                transform:translateY(-50%);
                background:rgba(255,255,255,0.05);
                border:1px solid rgba(255,255,255,0.05);
                color:#fff;
                width:50px;
                height:50px;
                border-radius:50%;
                cursor:pointer;
                font-size:1.2rem;
                transition:0.3s;
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:1001;
            }
            .fullscreen-overlay .nav-btn:hover {
                background:rgba(255,255,255,0.1);
            }
            .fullscreen-overlay .nav-btn.prev { left:20px; }
            .fullscreen-overlay .nav-btn.next { right:20px; }
            
            @media (max-width:480px) {
                .birthday-card { padding:1.2rem 0.8rem; border-radius:22px; max-height:98vh; }
                .name { font-size:2.2rem; }
                .name i { font-size:1.6rem; }
                .message { font-size:0.9rem; }
                .btn { font-size:0.75rem; padding:0.5rem 0.8rem; min-width:75px; }
                .cute-decoration { font-size:1.6rem; }
                .age-display { font-size:0.8rem; }
                .gallery-item { min-width:90px; max-width:110px; }
                .gallery-scroll { gap:10px; padding:4px 10px; }
                .fullscreen-overlay .nav-btn { width:40px; height:40px; font-size:1rem; }
                .fullscreen-overlay .nav-btn.prev { left:10px; }
                .fullscreen-overlay .nav-btn.next { right:10px; }
                .fullscreen-overlay .close-btn { top:15px; right:15px; width:40px; height:40px; font-size:1.5rem; }
            }
            @media (max-width:360px) {
                .name { font-size:1.8rem; }
                .name i { font-size:1.2rem; }
                .message { font-size:0.8rem; }
                .btn { font-size:0.7rem; padding:0.4rem 0.6rem; min-width:65px; }
                .gallery-item { min-width:70px; max-width:85px; }
                .birthday-card { padding:0.8rem 0.6rem; }
                .gallery-item .click-hint { width:30px; height:30px; font-size:1rem; }
            }
        </style>
    </head>
    <body>
        <div class="circle circle1"></div>
        <div class="circle circle2"></div>
        <?php if ($enableHearts): ?>
        <div class="tiny-hearts" id="tinyHearts"></div>
        <?php endif; ?>
        
        <div class="fullscreen-overlay" id="fullscreenOverlay">
            <button class="close-btn" onclick="closeFullscreen()"><i class="fas fa-times"></i></button>
            <button class="nav-btn prev" onclick="event.stopPropagation(); navigateFullscreen(-1)"><i class="fas fa-chevron-left"></i></button>
            <button class="nav-btn next" onclick="event.stopPropagation(); navigateFullscreen(1)"><i class="fas fa-chevron-right"></i></button>
            <img id="fullscreenImg" src="" alt="Fullscreen view">
            <div class="img-counter" id="fullscreenCounter">1 / 1</div>
        </div>
        
        <div class="birthday-card">
            <div class="cute-decoration">🎂 🎈 🎉</div>
            <div class="name">
                <i class="fas fa-crown"></i> <?php echo htmlspecialchars($name); ?> <i class="fas fa-heart"></i>
            </div>
            <div class="badge"><i class="fas fa-gift"></i> HAPPY BIRTHDAY <i class="fas fa-gift"></i></div>
            <div class="age-display"><i class="fas fa-calendar-alt"></i> <?php echo $age; ?> 🎂</div>
            
            <?php if ($hasGallery): ?>
            <div class="gallery-section" id="gallerySection">
                <div class="gallery-scroll" id="galleryScroll">
                    <?php foreach ($gallery as $index => $img): ?>
                    <div class="gallery-item" data-index="<?php echo $index; ?>" onclick="openFullscreen(<?php echo $index; ?>)">
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="Image <?php echo $index+1; ?>" loading="lazy" draggable="false">
                        <div class="img-overlay"><span><i class="fas fa-expand"></i> View</span></div>
                        <div class="click-hint"><i class="fas fa-search-plus"></i></div>
                        <span class="img-index"><?php echo $index+1; ?>/<?php echo count($gallery); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($gallery) > 1): ?>
                <div class="gallery-nav-buttons">
                    <button class="gnav" onclick="scrollGallery(-1)"><i class="fas fa-chevron-left"></i></button>
                    <button class="gnav" onclick="scrollGallery(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div class="message-box" id="messageBox">
                <div class="message" id="message">
                    <i class="fas fa-star" style="color:<?php echo $primary; ?>; font-size:0.8rem;"></i>
                    Aaj ka din sirf <span class="highlight">tumhara</span> hai!
                    <i class="fas fa-star" style="color:<?php echo $primary; ?>; font-size:0.8rem;"></i>
                    <br>
                    <span style="font-size:1.6rem;display:block;margin-top:4px;">🎂 <?php echo $age; ?> 🎂</span>
                </div>
                <div class="sub-message" id="subMessage">
                    <i class="fas fa-grin-hearts"></i> Tumhe bahut saara pyaar <i class="fas fa-grin-hearts"></i>
                </div>
            </div>
            
            <div class="btn-group">
                <button class="btn btn-pink" id="wishBtn"><i class="fas fa-gift"></i> Wish karo</button>
                <button class="btn btn-soft" id="surpriseBtn"><i class="fas fa-magic"></i> Surprise!</button>
            </div>
            <div class="footer-text">
                <i class="fas fa-heart" style="color:<?php echo $primary; ?>;"></i> <?php echo $age; ?> ka ho gaye! <i class="fas fa-heart" style="color:<?php echo $primary; ?>;"></i>
            </div>
        </div>
        
        <script>
            var galleryImages = <?php echo json_encode($gallery); ?>;
            var currentFullscreenIndex = 0;
            var isFullscreenOpen = false;
            
            function openFullscreen(index) {
                if (typeof index !== 'number') {
                    index = 0;
                }
                if (!galleryImages.length || index >= galleryImages.length) return;
                
                currentFullscreenIndex = index;
                var overlay = document.getElementById('fullscreenOverlay');
                var img = document.getElementById('fullscreenImg');
                var counter = document.getElementById('fullscreenCounter');
                
                if (galleryImages[currentFullscreenIndex]) {
                    img.src = galleryImages[currentFullscreenIndex];
                    counter.textContent = (currentFullscreenIndex + 1) + ' / ' + galleryImages.length;
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                    isFullscreenOpen = true;
                }
            }
            
            function closeFullscreen() {
                var overlay = document.getElementById('fullscreenOverlay');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
                isFullscreenOpen = false;
            }
            
            function navigateFullscreen(direction) {
                if (event) event.stopPropagation();
                currentFullscreenIndex += direction;
                if (currentFullscreenIndex < 0) currentFullscreenIndex = galleryImages.length - 1;
                if (currentFullscreenIndex >= galleryImages.length) currentFullscreenIndex = 0;
                
                var img = document.getElementById('fullscreenImg');
                var counter = document.getElementById('fullscreenCounter');
                
                if (galleryImages[currentFullscreenIndex]) {
                    img.style.transform = 'scale(0.8)';
                    setTimeout(function() {
                        img.src = galleryImages[currentFullscreenIndex];
                        counter.textContent = (currentFullscreenIndex + 1) + ' / ' + galleryImages.length;
                        setTimeout(function() {
                            img.style.transform = 'scale(1)';
                        }, 50);
                    }, 150);
                }
            }
            
            document.addEventListener('keydown', function(e) {
                if (!isFullscreenOpen) return;
                if (e.key === 'Escape') closeFullscreen();
                if (e.key === 'ArrowLeft') navigateFullscreen(-1);
                if (e.key === 'ArrowRight') navigateFullscreen(1);
            });
            
            document.getElementById('fullscreenOverlay').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeFullscreen();
                }
            });
            
            // Gallery Auto Scroll
            (function() {
                var scrollEl = document.getElementById('galleryScroll');
                if (!scrollEl) return;
                var items = scrollEl.querySelectorAll('.gallery-item');
                if (items.length <= 1) return;
                
                var currentIndex = 0;
                var autoScrollInterval = null;
                
                function scrollToIndex(index) {
                    if (index >= items.length) index = 0;
                    if (index < 0) index = items.length - 1;
                    currentIndex = index;
                    var item = items[currentIndex];
                    if (item) {
                        item.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    }
                }
                
                function nextSlide() {
                    scrollToIndex(currentIndex + 1);
                }
                
                function startAutoScroll() {
                    if (autoScrollInterval) clearInterval(autoScrollInterval);
                    autoScrollInterval = setInterval(nextSlide, 3500);
                }
                
                function stopAutoScroll() {
                    if (autoScrollInterval) {
                        clearInterval(autoScrollInterval);
                        autoScrollInterval = null;
                    }
                }
                
                startAutoScroll();
                
                scrollEl.addEventListener('mouseenter', stopAutoScroll);
                scrollEl.addEventListener('mouseleave', startAutoScroll);
                scrollEl.addEventListener('touchstart', stopAutoScroll, { passive: true });
                scrollEl.addEventListener('touchend', startAutoScroll, { passive: true });
                
                var isDown = false;
                var startX = 0;
                var scrollLeft = 0;
                
                scrollEl.addEventListener('mousedown', function(e) {
                    isDown = true;
                    startX = e.pageX - scrollEl.offsetLeft;
                    scrollLeft = scrollEl.scrollLeft;
                    scrollEl.style.cursor = 'grabbing';
                    stopAutoScroll();
                });
                
                scrollEl.addEventListener('mouseleave', function() {
                    if (isDown) {
                        isDown = false;
                        scrollEl.style.cursor = 'grab';
                        setTimeout(startAutoScroll, 3000);
                    }
                });
                
                scrollEl.addEventListener('mouseup', function() {
                    if (isDown) {
                        isDown = false;
                        scrollEl.style.cursor = 'grab';
                        setTimeout(startAutoScroll, 3000);
                    }
                });
                
                scrollEl.addEventListener('mousemove', function(e) {
                    if (!isDown) return;
                    e.preventDefault();
                    var x = e.pageX - scrollEl.offsetLeft;
                    var walk = (x - startX) * 2;
                    scrollEl.scrollLeft = scrollLeft - walk;
                });
                
                var touchStartX = 0;
                var touchScrollLeft = 0;
                
                scrollEl.addEventListener('touchstart', function(e) {
                    touchStartX = e.touches[0].pageX - scrollEl.offsetLeft;
                    touchScrollLeft = scrollEl.scrollLeft;
                    stopAutoScroll();
                }, { passive: true });
                
                scrollEl.addEventListener('touchmove', function(e) {
                    var x = e.touches[0].pageX - scrollEl.offsetLeft;
                    var walk = (x - touchStartX) * 2;
                    scrollEl.scrollLeft = touchScrollLeft - walk;
                }, { passive: true });
                
                scrollEl.addEventListener('touchend', function() {
                    setTimeout(startAutoScroll, 3000);
                }, { passive: true });
            })();
            
            window.scrollGallery = function(direction) {
                var scrollEl = document.getElementById('galleryScroll');
                if (!scrollEl) return;
                var scrollAmount = 160;
                scrollEl.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
            };
            
            // Birthday Logic
            (function() {
                var tinyContainer = document.getElementById('tinyHearts');
                var hearts = ['♥', '♡', '❤'];
                var enableHearts = <?php echo $enableHearts ? 'true' : 'false'; ?>;
                
                if (enableHearts && tinyContainer) {
                    function createTinyHeart() {
                        var el = document.createElement('div');
                        el.className = 'tiny-heart';
                        el.textContent = hearts[Math.floor(Math.random() * hearts.length)];
                        el.style.left = Math.random() * 100 + '%';
                        el.style.fontSize = (0.4 + Math.random() * 0.6) + 'rem';
                        el.style.animationDuration = (15 + Math.random() * 18) + 's';
                        el.style.animationDelay = Math.random() * 10 + 's';
                        el.style.opacity = 0.02 + Math.random() * 0.03;
                        el.style.color = 'hsl(' + (320 + Math.random() * 40) + ', 80%, 70%)';
                        tinyContainer.appendChild(el);
                        setTimeout(function() { if (el.parentNode) el.remove(); }, 35000);
                    }
                    for (var i = 0; i < 8; i++) setTimeout(createTinyHeart, i * 300);
                    setInterval(createTinyHeart, 1000);
                }
                
                var wishes = <?php echo json_encode($wishes); ?>;
                var finalMessage = <?php echo json_encode($finalMessage); ?>;
                var age = <?php echo $age; ?>;
                var primaryColor = <?php echo json_encode($primary); ?>;
                
                var wishIndex = 0;
                var messageEl = document.getElementById('message');
                var subEl = document.getElementById('subMessage');
                var messageBox = document.getElementById('messageBox');
                
                function triggerBirthdayCelebration() {
                    var emojis = ['🎂', '🎈', '🎉', '🎁', '🎊', '🥳', '🎇', '✨', '⭐', '💖', '❤️', '🎀', '🎵', '🎶'];
                    var colors = ['#ff4d8f', '#d66aff', '#ff77b0', '#ffb84d', '#4dc9ff', '#ff6b6b', '#ffd93d', '#6bcb77', '#ff77b0', '#d9b3ff'];
                    
                    for (var i = 0; i < 40; i++) {
                        (function(i) {
                            setTimeout(function() {
                                var emoji = document.createElement('div');
                                emoji.className = 'birthday-emoji-drop';
                                emoji.textContent = emojis[Math.floor(Math.random() * emojis.length)];
                                emoji.style.left = (2 + Math.random() * 96) + '%';
                                emoji.style.fontSize = (1.2 + Math.random() * 2.5) + 'rem';
                                emoji.style.animationDuration = (2.5 + Math.random() * 3) + 's';
                                emoji.style.animationDelay = (Math.random() * 1.5) + 's';
                                emoji.style.color = colors[Math.floor(Math.random() * colors.length)];
                                document.body.appendChild(emoji);
                                setTimeout(function() { if (emoji.parentNode) emoji.remove(); }, 5000);
                            }, i * 40);
                        })(i);
                    }
                    
                    for (var i = 0; i < 20; i++) {
                        (function(i) {
                            setTimeout(function() {
                                var heart = document.createElement('div');
                                heart.className = 'birthday-heart-burst';
                                heart.textContent = ['❤️', '💖', '💗', '💘', '💝'][Math.floor(Math.random() * 5)];
                                heart.style.left = (10 + Math.random() * 80) + '%';
                                heart.style.top = (10 + Math.random() * 80) + '%';
                                heart.style.fontSize = (1.8 + Math.random() * 3) + 'rem';
                                heart.style.color = colors[Math.floor(Math.random() * colors.length)];
                                heart.style.textShadow = '0 0 30px ' + colors[Math.floor(Math.random() * colors.length)];
                                var angle = Math.random() * 2 * Math.PI;
                                var distance = 100 + Math.random() * 180;
                                heart.style.setProperty('--tx', Math.cos(angle) * distance + 'px');
                                heart.style.setProperty('--ty', Math.sin(angle) * distance + 'px');
                                document.body.appendChild(heart);
                                setTimeout(function() { if (heart.parentNode) heart.remove(); }, 2500);
                            }, i * 60);
                        })(i);
                    }
                    
                    for (var i = 0; i < 30; i++) {
                        (function(i) {
                            setTimeout(function() {
                                var confetti = document.createElement('div');
                                confetti.className = 'birthday-confetti';
                                confetti.style.left = (2 + Math.random() * 96) + '%';
                                confetti.style.top = '-5%';
                                confetti.style.width = (6 + Math.random() * 10) + 'px';
                                confetti.style.height = (6 + Math.random() * 10) + 'px';
                                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                                confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
                                confetti.style.animationDuration = (2.5 + Math.random() * 3) + 's';
                                confetti.style.animationDelay = (Math.random() * 1.5) + 's';
                                confetti.style.boxShadow = '0 0 15px ' + colors[Math.floor(Math.random() * colors.length)];
                                document.body.appendChild(confetti);
                                setTimeout(function() { if (confetti.parentNode) confetti.remove(); }, 5000);
                            }, i * 50);
                        })(i);
                    }
                }
                
                function showWish() {
                    var wish = wishes[wishIndex % wishes.length];
                    messageEl.innerHTML = '<i class="fas fa-star" style="color:' + primaryColor + '; font-size:0.8rem;"></i> ' + wish + ' <i class="fas fa-star" style="color:' + primaryColor + '; font-size:0.8rem;"></i><br><span style="font-size:1.6rem;display:block;margin-top:4px;">🎂 ' + age + ' 🎂</span>';
                    subEl.innerHTML = '<i class="fas fa-grin-hearts"></i> ' + finalMessage + ' <i class="fas fa-grin-hearts"></i>';
                    messageBox.classList.remove('pop-in');
                    void messageBox.offsetWidth;
                    messageBox.classList.add('pop-in');
                    wishIndex++;
                    
                    if (enableHearts && tinyContainer) {
                        for (var i = 0; i < 8; i++) {
                            (function(i) {
                                setTimeout(function() {
                                    var h = document.createElement('div');
                                    h.className = 'tiny-heart';
                                    h.textContent = ['❤', '♥', '💕', '✨'][Math.floor(Math.random() * 4)];
                                    h.style.left = (20 + Math.random() * 60) + '%';
                                    h.style.top = (20 + Math.random() * 60) + '%';
                                    h.style.fontSize = (0.6 + Math.random() * 1) + 'rem';
                                    h.style.animationDuration = (2 + Math.random() * 2) + 's';
                                    h.style.animationDelay = '0s';
                                    h.style.opacity = '0.2';
                                    h.style.color = 'hsl(' + (320 + Math.random() * 50) + ', 90%, 70%)';
                                    tinyContainer.appendChild(h);
                                    setTimeout(function() { if (h.parentNode) h.remove(); }, 3000);
                                }, i * 50);
                            })(i);
                        }
                    }
                }
                
                function doSurprise() {
                    messageEl.innerHTML = '<i class="fas fa-heart" style="color:' + primaryColor + '; font-size:1.6rem;"></i><span style="display:block;font-size:1.5rem;font-weight:800;color:#ffd9f2;margin:4px 0;">🎉 SURPRISE! 🎉</span><span style="font-size:1.6rem;display:block;margin-top:4px;">🎂 ' + age + ' 🎂</span><span style="display:block;font-size:0.95rem;color:#e0c8f0;margin-top:4px;"><i class="fas fa-star"></i> ' + finalMessage + ' <i class="fas fa-star"></i></span><span style="display:block;font-size:1.8rem;margin-top:4px;">🎉 🎊 🥳 🎂 🎈</span>';
                    subEl.innerHTML = '<i class="fas fa-heart" style="color:' + primaryColor + ';"></i> ' + finalMessage + ' <i class="fas fa-heart" style="color:' + primaryColor + ';"></i>';
                    messageBox.classList.remove('pop-in');
                    void messageBox.offsetWidth;
                    messageBox.classList.add('pop-in');
                    triggerBirthdayCelebration();
                }
                
                document.getElementById('wishBtn').addEventListener('click', showWish);
                document.getElementById('surpriseBtn').addEventListener('click', function(e) {
                    e.preventDefault();
                    doSurprise();
                });
                
                setTimeout(showWish, 500);
            })();
        </script>
    </body>
    </html>
    <?php
    exit;
}
?>