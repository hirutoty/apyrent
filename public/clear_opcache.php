<?php
// Clear Opcache via web request
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ Opcache cleared successfully!";
} else {
    echo "⚠️ Opcache not enabled";
}
