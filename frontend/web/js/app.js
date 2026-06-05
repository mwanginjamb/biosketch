// Button micro-interaction
document.querySelectorAll('button').forEach(btn => {
    btn.addEventListener('click', function () {
        this.classList.add('opacity-70');
        setTimeout(() => this.classList.remove('opacity-70'), 150);
    });
});

// Input validation feedback (visual only)
document.querySelectorAll('input, textarea').forEach(input => {
    input.addEventListener('blur', function () {
        if (this.value.length > 0) {
            this.classList.add('border-secondary');
            this.classList.remove('border-outline-variant');
        } else {
            this.classList.remove('border-secondary');
            this.classList.add('border-outline-variant');
        }
    });
});

// IntersectionObserver for view layout
const sections = document.querySelectorAll('.section-anchor');
if (sections.length) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100', 'translate-y-0');
                entry.target.classList.remove('opacity-50', 'translate-y-4');
            }
        });
    }, { threshold: 0.3 });
    sections.forEach(section => {
        section.classList.add('transition-all', 'duration-500', 'opacity-50', 'translate-y-4');
        observer.observe(section);
    });
}

// Password toggle for login form
const toggleBtn = document.querySelector('#toggle-password');
const passwordInput = document.getElementById('password');
if (toggleBtn && passwordInput) {
    toggleBtn.addEventListener('click', () => {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        toggleBtn.innerText = isPassword ? 'visibility_off' : 'visibility';
    });
}