document.addEventListener('DOMContentLoaded', function () {
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.querySelector('.nav-list');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('is-open');
    });
  }

  document.querySelectorAll('.tab-group').forEach(function (group) {
    var links = group.querySelectorAll('.tab-link');
    links.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var targetId = link.getAttribute('data-tab-target');
        var panel = document.getElementById(targetId);
        if (!panel) return;
        links.forEach(function (l) { l.classList.remove('is-active'); });
        group.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('is-active'); });
        link.classList.add('is-active');
        panel.classList.add('is-active');
      });
    });
  });

  document.querySelectorAll('[data-modal-target]').forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      var modal = document.getElementById(trigger.getAttribute('data-modal-target'));
      if (modal) modal.classList.add('is-open');
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(function (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal || e.target.closest('.modal-close')) {
        modal.classList.remove('is-open');
      }
    });
  });
});
