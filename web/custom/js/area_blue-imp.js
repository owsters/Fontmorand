//for Blue-Imp

document.getElementById('links_creuse').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};
document.getElementById('links_brenne').onclick = function (event) {
event = event || window.event;
var target = event.target || event.srcElement,
	link = target.src ? target.parentNode : target,
	options = {index: link, event: event, onslide: function (index, slide) {
			var text = this.list[index].getAttribute('data-description'),
					node = this.container.find('.description');
			node.empty();
			if (text) {
					node[0].appendChild(document.createTextNode(text));
			}
	} },
	links = this.getElementsByTagName('a');
	blueimp.Gallery(links, options);
};