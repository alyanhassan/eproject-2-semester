// ============================================================
// VAXORA — Main JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // Scroll progress bar
  const progressBar = document.getElementById('scroll-progress');
  if (progressBar) {
    window.addEventListener('scroll', () => {
      const scrollTop = document.documentElement.scrollTop;
      const docHeight = document.documentElement.scrollHeight - window.innerHeight;
      progressBar.style.width = (scrollTop / docHeight * 100) + '%';
    });
  }

  // Navbar frosted glass on scroll
  const navbar = document.querySelector('.navbar');
  if (navbar) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 80) {
        navbar.style.background = 'rgba(255,255,255,0.97)';
        navbar.style.boxShadow = '0 2px 20px rgba(28,39,6,0.08)';
      } else {
        navbar.style.background = 'rgba(255,255,255,0.96)';
        navbar.style.boxShadow = 'none';
      }
    });
  }

  // Mobile hamburger
  const hamburger = document.querySelector('.hamburger');
  const navLinks  = document.querySelector('.nav-links');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', () => {
      navLinks.classList.toggle('open');
    });
  }

  // Accordion (FAQ)
  document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', () => {
      const body   = header.nextElementSibling;
      const isOpen = body.classList.contains('open');
      document.querySelectorAll('.accordion-body').forEach(b => b.classList.remove('open'));
      document.querySelectorAll('.accordion-icon').forEach(i => i.textContent = '+');
      if (!isOpen) {
        body.classList.add('open');
        header.querySelector('.accordion-icon').textContent = '−';
      }
    });
  });

  // Filter tabs (hospitals, vaccines)
  document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      const group = tab.dataset.group || 'default';
      document.querySelectorAll(`.filter-tab[data-group="${group}"]`).forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const target = tab.dataset.filter;
      document.querySelectorAll('[data-city]').forEach(card => {
        if (target === 'all' || card.dataset.city === target) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // Search filter (hospitals)
  const searchInput = document.getElementById('hospitalSearch');
  if (searchInput) {
    searchInput.addEventListener('input', () => {
      const q = searchInput.value.toLowerCase().trim();
      document.querySelectorAll('[data-hospital-name]').forEach(card => {
        const name = card.dataset.hospitalName.toLowerCase();
        card.style.display = name.includes(q) ? '' : 'none';
      });
    });
  }

  // CountUp animation for stats
  const countEls = document.querySelectorAll('[data-countup]');
  if (countEls.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el     = entry.target;
          const target = parseInt(el.dataset.countup);
          const suffix = el.dataset.suffix || '';
          const dur    = 2000;
          let start    = 0;
          const step   = target / (dur / 16);
          const timer  = setInterval(() => {
            start += step;
            if (start >= target) { el.textContent = target + suffix; clearInterval(timer); }
            else { el.textContent = Math.floor(start) + suffix; }
          }, 16);
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.5 });
    countEls.forEach((el, i) => {
      setTimeout(() => observer.observe(el), i * 150);
    });
  }

  // Scroll-reveal animations
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length > 0) {
    const revealObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
          setTimeout(() => entry.target.classList.add('visible'), entry.target.dataset.delay || 0);
          revealObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1 });
    reveals.forEach(el => revealObserver.observe(el));
  }

  // Dashboard sidebar toggle (mobile)
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar       = document.querySelector('.sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
  }

  // Confirm delete
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', (e) => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // Auto-dismiss alerts
  setTimeout(() => {
    document.querySelectorAll('.flash-msg, .alert-auto').forEach(el => {
      el.style.transition = 'opacity 0.5s';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    });
  }, 4000);

});

// Reveal CSS helper
const style = document.createElement('style');
style.textContent = `
  .reveal { opacity:0; transform:translateY(28px); transition:opacity 0.6s ease, transform 0.6s ease; }
  .reveal.visible { opacity:1; transform:none; }
`;
document.head.appendChild(style);
