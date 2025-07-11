// Simple test script to debug timer display
document.addEventListener('DOMContentLoaded', function() {
    console.log('Timer test script loaded');
    
    // Create a simple timer element
    const timer = document.createElement('div');
    timer.id = 'test-timer';
    timer.style.cssText = `
        position: fixed;
        top: 50px;
        right: 10px;
        background-color: red;
        color: white;
        padding: 10px;
        border-radius: 4px;
        z-index: 9999;
        font-weight: bold;
    `;
    timer.textContent = 'TEST TIMER';
    
    // Wait for body to be ready
    if (document.body) {
        console.log('Body available, adding test timer');
        document.body.appendChild(timer);
    } else {
        console.error('Body not available during DOMContentLoaded');
        
        // Try again after a delay
        setTimeout(() => {
            if (document.body) {
                console.log('Body available after delay, adding test timer');
                document.body.appendChild(timer);
            } else {
                console.error('Body still not available after delay');
            }
        }, 500);
    }
});

// Also try with window.onload
window.onload = function() {
    console.log('Window loaded event fired');
    
    const existingTimer = document.getElementById('test-timer');
    if (!existingTimer) {
        console.log('Creating backup timer in window.onload');
        const backupTimer = document.createElement('div');
        backupTimer.id = 'backup-timer';
        backupTimer.style.cssText = `
            position: fixed;
            top: 100px;
            right: 10px;
            background-color: green;
            color: white;
            padding: 10px;
            border-radius: 4px;
            z-index: 9999;
            font-weight: bold;
        `;
        backupTimer.textContent = 'BACKUP TIMER';
        document.body.appendChild(backupTimer);
    } else {
        console.log('Test timer already exists in window.onload');
    }
};
