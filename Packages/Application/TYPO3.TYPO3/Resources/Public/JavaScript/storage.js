define(['vie/entity', 'backbone'], function(Entity) {
	if (window._requirejsLoadingTrace) window._requirejsLoadingTrace.push('storage');

	Backbone.sync = function(method, model, options) {
		var methods = {
			'create': function(model, options) {
				console.log("CREATE", arguments);
			},
			'read': function(model, options) {
				console.log("READ", arguments);
			},
			'update': function(model, options) {
				var nodeJson = this._convertModelToJson(model);

				window.TYPO3_TYPO3_Service_ExtDirect_V1_Controller_NodeController.update(nodeJson, function(result) {
						// when we save a node, it could be the case that it was in
						// live workspace beforehand, but because of some modifications,
						// is now copied into the user's workspace.
						// That's why we need to update the (possibly changed) workspace
						// name in the block.
						// Furthermore, we do not want event listeners to be fired, as otherwise, the
						// contentelement would be redrawn leading to a loss of the current editing cursor position.
					model.set('typo3:__workspacename', result.data.workspaceNameOfNode, {silent: true});
					if (options && options.success) {
						options.success(model, result);
					}
				});
			},
			'delete': function(model, options) {
				console.log("DELETE", arguments);
			},
			_convertModelToJson: function(model) {
				var contextPath = model.fromReference(model.id);
				var attributes = Entity.extractAttributesFromVieEntity(model, function filter(k, v) {
						// skip internal properties starting with __
					if (k[0] === '_' && k[1] === '_') {
						return false;
					}
					return true;
				});
				attributes['__contextNodePath'] = contextPath;
				return attributes;
			}
		};

		methods[method].call(methods, model, options);
	};
});