// (1) Dark Mode & Local Storage
document.addEventListener('DOMContentLoaded', function() {
    const darkModeBtn = document.getElementById('darkModeBtn');
    if (!darkModeBtn) return;
    
    const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
    let currentTheme = localStorage.getItem('colorScheme');
    
    if (!currentTheme) {
        currentTheme = prefersDarkScheme.matches ? 'dark' : 'light';
        localStorage.setItem('colorScheme', currentTheme);
    }
    
    if (currentTheme === 'dark') {
        document.body.classList.add('dark-mode');
        darkModeBtn.textContent = 'Light Mode';
    } else {
        darkModeBtn.textContent = 'Dark Mode';
    }
    
    darkModeBtn.addEventListener('click', function () {
        document.body.classList.toggle('dark-mode');
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('colorScheme', 'dark');
            darkModeBtn.textContent = 'Light Mode';
        } else {
            localStorage.setItem('colorScheme', 'light');
            darkModeBtn.textContent = 'Dark Mode';
        }
    });

    // Initialize accordions and answer buttons
    initAccordions();
});

// (2) Initialize all interactive elements
function initAccordions() {
    // Area accordion buttons
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            const accordionId = this.getAttribute('data-accordion-id');
            if (accordionId) {
                toggleAccordion(accordionId);
            }
        });
    });
    
    // Answer toggle buttons
    document.querySelectorAll('.question-button').forEach(button => {
        button.addEventListener('click', function() {
            toggleAnswer(this);
        });
    });
}

// (3) Area Accordion Toggle
function toggleAccordion(accordionId) {
    const content = document.getElementById(accordionId);
    if (!content) return;
    
    // Toggle open class which controls visibility in CSS
    content.classList.toggle('open');
    
    // Update button text/icon if needed
    const button = document.querySelector(`[data-accordion-id="${accordionId}"]`);
    if (button) {
        if (content.classList.contains('open')) {
            button.setAttribute('aria-expanded', 'true');
        } else {
            button.setAttribute('aria-expanded', 'false');
        }
    }
}

// (4) Answer Toggle
function toggleAnswer(btn) {
    const questionId = btn.closest('.question').getAttribute('id');
    const answerId = btn.getAttribute('data-answer-id');
    const answerBox = document.getElementById(answerId);
    
    if (!answerBox) return;
    
    // Toggle the open class to control visibility through CSS
    answerBox.classList.toggle('open');
    
    // Update button text based on state
    if (answerBox.classList.contains('open')) {
        btn.textContent = "Antwort ausblenden";
        btn.setAttribute('aria-expanded', 'true');
    } else {
        btn.textContent = "Antwort anzeigen";
        btn.setAttribute('aria-expanded', 'false');
    }
}