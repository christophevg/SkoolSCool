/**
 * messages.js
 * simple pop-up messages solution ;-)
 * @author Christophe VG
 */

(function (globals) {

	var container,
			index = 0;

	function addInfo(msg) {
		add("info", msg);
	}

	function addWarning(msg) {
		add("warning", msg);
	}

	function addCritical(msg) {
		add("critical", msg);
	}
	
	/* private */ function add(type, msg) {
		var div = document.createElement('div');
		div.id = "message-" + index++;
		div.className = "message " + type;
		div.onClick = function() { this.style.display='none;'; };
		div.innerHTML = msg;

		getContainer().appendChild(div);
		window.setTimeout( function() { div.style.display = "none"; }, 4000 );
	}

	/* private */ function getContainer() {
		// lazy initialization
		if( container == null ) {
			container = document.createElement('div');
			container.className="messages";
			document.body.appendChild(container);
		}
		return container;
	}
	
	globals.Messages = {
		"addInfo"     : addInfo,
		"addWarning"  : addWarning,
		"addCritical" : addCritical
	}

})(window);
