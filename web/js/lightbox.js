document.addEventListener('DOMContentLoaded', function () {
  var overlay = document.createElement('div');
  overlay.className = 'lightbox-overlay';
  overlay.innerHTML =
    '<button class="lightbox-close" aria-label="Close">&times;</button>' +
    '<button class="lightbox-prev" aria-label="Previous">&lsaquo;</button>' +
    '<img class="lightbox-image" src="" alt="">' +
    '<p class="lightbox-caption"></p>' +
    '<button class="lightbox-next" aria-label="Next">&rsaquo;</button>';
  document.body.appendChild(overlay);

  var img = overlay.querySelector('.lightbox-image');
  var caption = overlay.querySelector('.lightbox-caption');
  var items = [];
  var index = 0;

  function show(i) {
    index = (i + items.length) % items.length;
    var item = items[index];
    img.src = item.getAttribute('href');
    caption.textContent = item.getAttribute('data-caption') || '';
    img.alt = caption.textContent;
    overlay.classList.add('is-open');
  }

  function close() {
    overlay.classList.remove('is-open');
  }

  document.querySelectorAll('.gallery').forEach(function (gallery) {
    var links = Array.prototype.slice.call(gallery.querySelectorAll('.gallery-item'));
    links.forEach(function (link, i) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        items = links;
        show(i);
      });
    });
  });

  overlay.querySelector('.lightbox-close').addEventListener('click', close);
  overlay.querySelector('.lightbox-prev').addEventListener('click', function () { show(index - 1); });
  overlay.querySelector('.lightbox-next').addEventListener('click', function () { show(index + 1); });
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) close();
  });
  document.addEventListener('keydown', function (e) {
    if (!overlay.classList.contains('is-open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') show(index - 1);
    if (e.key === 'ArrowRight') show(index + 1);
  });
});
