
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Satisfy&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/admin-base.css?v=1.6">
<script src="js/admin-utils.js?v=1.3"></script>
<script src="js/loading.js?v=1.1"></script>

<style>
    /* PREMIUM SELECTIVE ANTI-AD PROTECTION (ANTIGRAVITY) */
    /* Target only known InfinityFree ad IDs/classes, never touch Google domains */
    #sb98124, #sb98124_image, #sb98124_close, .tutup2,
    div[id*="sb"][style*="fixed"], 
    div[class*="sb"][style*="fixed"],
    a[href*="infinityfree"], 
    center a[title*="Free Web Hosting"],
    div[style*="z-index: 99999"], 
    .disclaimer {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
        position: absolute !important;
        left: -9999px !important;
        visibility: hidden !important;
    }
</style>

<script>
    /* Nuclear Anti-Ad Injection Cleanup (Selective) */
    (function(){
        const cleanup = () => {
            const selectors = [
                '#sb98124', '.tutup2', 'div[id^="sb"]', 
                'a[href*="infinityfree"]', 'center a[title*="Hosting"]',
                'div[style*="z-index: 99999"]', 'div[style*="position: fixed"][style*="99999"]'
            ];
            selectors.forEach(s => {
                document.querySelectorAll(s).forEach(el => {
                    if (!el.innerText.includes("AI") && !el.id.includes("chat")) {
                        el.remove();
                    }
                });
            });
        };
        cleanup();
        setInterval(cleanup, 1000);
        window.addEventListener('load', cleanup);
    })();
</script>

<?php

if(isset($conn)) {
    try {
        $themeRes = $conn->query("SELECT theme_config FROM studio_settings WHERE id = 1");
        if($themeRes && $themeRes->num_rows > 0) {
            $themeCfg = json_decode($themeRes->fetch_assoc()['theme_config'] ?? '{}', true);
            
            $mode = $themeCfg['mode'] ?? 'light';
            $colors = $themeCfg['colors'] ?? [];
            
            
            $cssVars = "";
            if(!empty($colors)) {
                if(isset($colors['primary'])) {
                    $p = $colors['primary'];
                    $cssVars .= "--primary: $p; --primary-indigo: $p; --primary-hover: $p; ";
                    
                }
                if(isset($colors['bg_main'])) $cssVars .= "--bg-main: {$colors['bg_main']}; ";
                if(isset($colors['bg_card'])) $cssVars .= "--bg-card: {$colors['bg_card']}; ";
            }
            
            echo "<style>";
            echo ":root { $cssVars }";
            
            if($mode === 'dark') {
                echo "body.dark-mode { $cssVars }";
            }
            echo "</style>";

            
            if($mode === 'dark') {
                echo "<script>
                    document.documentElement.classList.add('dark-mode');
                    window.addEventListener('DOMContentLoaded', () => { 
                        document.body.classList.add('dark-mode'); 
                        localStorage.setItem('darkMode', 'enabled');
                        const t = document.getElementById('darkModeToggle');
                        if(t) t.checked = true;
                    });
                </script>";
            } elseif($mode === 'light') {
                echo "<script>
                    document.documentElement.classList.remove('dark-mode');
                    window.addEventListener('DOMContentLoaded', () => { 
                        document.body.classList.remove('dark-mode'); 
                        localStorage.setItem('darkMode', 'disabled');
                        const t = document.getElementById('darkModeToggle');
                        if(t) t.checked = false;
                    });
                </script>";
            }
        }
    } catch (Exception $e) {
        
        
    }
}
?>
