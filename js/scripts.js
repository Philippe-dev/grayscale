/*!
 * Start Bootstrap - Grayscale v7.0.1 (https://startbootstrap.com/theme/grayscale)
 * Copyright 2013-2021 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-grayscale/blob/master/LICENSE)
 */
//
// Scripts
//

window.addEventListener('DOMContentLoaded', (event) => {
  // Navbar shrink function
  const navbarShrink = () => {
    const navbarCollapsible = document.body.querySelector('#menubar');
    if (!navbarCollapsible) {
      return;
    }
    if (window.scrollY === 0) {
      navbarCollapsible.classList.remove('navbar-shrink');
    } else {
      navbarCollapsible.classList.add('navbar-shrink');
    }
  };

  // Shrink the navbar
  navbarShrink();

  // Shrink the navbar when page is scrolled
  document.addEventListener('scroll', navbarShrink);

  // Activate Bootstrap scrollspy on the main nav element
  const menubar = document.body.querySelector('#menubar');
  if (menubar) {
    new bootstrap.ScrollSpy(document.body, {
      target: '#menubar',
      offset: 74,
    });
  }

  // Collapse responsive navbar when toggler is visible
  const navbarToggler = document.body.querySelector('.navbar-toggler');
  const responsiveNavItems = [].slice.call(document.querySelectorAll('#navbarResponsive .nav-link'));
  responsiveNavItems.map((responsiveNavItem) => {
    responsiveNavItem.addEventListener('click', () => {
      if (window.getComputedStyle(navbarToggler).display !== 'none') {
        navbarToggler.click();
      }
    });
  });
});
