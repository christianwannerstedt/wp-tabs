;(function ($, window, document, undefined){

	$(document).ready(function($){

		tinyMCE.init({
			mode: "textareas",
			theme: "advanced",
			width: "600px",
			height: "400px",
			theme_advanced_buttons3: ""
		});

		// Tab Model
		var tab = Backbone.Model.extend({
		    defaults: {
		    	position: 0,
		        id: "-",
		        title: "",
		        body: ""
		    },
		    initialize: function(){},
		    sync: function(method, model, options){
		    	if (method == "update"){
					var	data = {
						action: "update_tab",
						tab_id: this.id,
						title: model.attributes.title,
						body: model.attributes.body
					};
					$.post(ajaxurl, data, function(response){
						if (response == 200){
							$("#tab-"+ data.tab_id +"-updated").fadeIn("slow", function(){
								$("#tab-"+ data.tab_id +"-updated").fadeOut("slow");
							});
						} else {
							alert("An error occured and the tab was not updated.. Please try again..");
						}
					});
				}

		    	// Delete tab
		    	else if (method == "delete"){
			    	if (confirm("Do you really want to delete this tab?")){
						$.post(ajaxurl, {action: "delete_tab", tab_id: this.id }, function(response) {
							if (response == 200){
								if (options.success) options.success(model);
							} else {
								alert("An error occured and the tab could not be deleted..");
							}
						});
					}
		    	}
		    }
		});

		// tabs collection
		var tabs = Backbone.Collection.extend({
		    model: tab,
		    initialize: function (models, options) {
			}
		});

		// Edit list view
		var tabcontainerView = Backbone.View.extend({
			initialize: function () {
				this.template       = _.template($('#tabs-template').html());
	        	this.head_template = _.template($('#tab-head-template').html());
	        	this.inner_template = _.template($('#tab-template').html());

				this.collection.on("add", this.addtab, this);
				this.collection.on("remove", this.removetab, this);
			},
			setupSorting: function(){
				var _this = this;
				$("#tab-header").sortable({
					update: function(event, ui){
						_this.savePosition();
					}
				});
				return this;
			},
			events: {
				"click .delete-tab-button":  "deletetab",
				"click .update-tab-button": "updatetab",
				"click .tab": "selecttab"
			},
			deletetab: function(e){
				var tab_id = $(e.currentTarget).attr("id").split("-")[2],
					tab = this.collection.get(tab_id);
				tab.destroy({
					wait: true,
					success: function(model, response){
					}
				});
			},
			updatetab: function(e){
				var tab_id = $(e.currentTarget).attr("id").split("-")[2],
					tab = this.collection.get(tab_id);
				tab.save({
					title: $("#tab-"+ tab_id +"-title").val(),
					body: tinymce.get("tab-"+ tab_id +"-body").getContent()
				});
			},
			selecttab: function(e){
				var $tab = $(e.currentTarget);
				if (!$tab.hasClass("active")){
					$(".tab").removeClass("active");
					$(".tab-content").removeClass("active");
					$tab.addClass("active");

					var $active_content = $(".tab-content[data-tab='" + $tab.attr("data-tab") + "']");
					$active_content.addClass("active");
					//tinyMCE.execCommand('mceAddControl', false, 'textarea');
				}
			},
			addtab: function(tab){
				var i = $(".tab").size();
				while (true){
					if ($("[data-tab='"+ i +"']").size() > 0){
						i++;
					} else {
						break;
					}
				}
				$("#tab-header", this.el).append(
					this.head_template($.extend({i: i}, tab.toJSON()))
				);
				$("#tab-content-container", this.el).append(
					this.inner_template($.extend({i: i}, tab.toJSON()))
				);
			},
			removetab: function(tab){
				var _this = this;
				$("#tab-content-"+ tab.id, this.el).fadeOut();
				$("#tab-"+ tab.id, this.el).fadeOut(function(){
					$("#tab-"+ tab.id, this.el).remove();
					_this.savePosition();
				});
			},
			savePosition: function(){
				var ids = [];
				$("#tab-header .tab").each(function(index){
					ids.push( $(this).attr("data-id") );
				});
				$.post(ajaxurl, {action: "update_tab_position", ids: ids.join(",") }, function(response) {
					if (response != 200){
						alert("An error occured and the new position could not be saved..");
					}
				});
			},
			render: function(){
				this.$el.html( this.template( {tabs: this.collection.toJSON()} ) );
				return this;
			}
		});


		// Create the backbone view
		var tab_container_view = new tabcontainerView({
			el: $("#edit-tabs-list-container"),
			collection: new tabs(tabs_json)
		}).render().setupSorting();


		// Setup tab container form event
		$("#form-update-wptabs-settings").submit(function(e){
			e.preventDefault();
			$("#wptabs-update-settings-submit").attr("disabled", "disabled");
			$("#wptabs-update-settings-saving").show();

			var data = {
				action: "update_tab_container_settings",
				tab_container_id: tab_container_id,
				title: $("#tab-container-title").val()
			};

			$.post(ajaxurl, data, function(response) {
				$("#wptabs-update-settings-submit").removeAttr("disabled");
				$("#wptabs-update-settings-saving").fadeOut();
				if (response != 200){
					alert("An error occured and the setting was not updated.. Please try again..");
				}
			});
		});


		$("#form-create-tab").on("submit", function(e){
			e.preventDefault();
			$("#wptabs-create-submit").attr("disabled", "disabled");
			$("#wptabs-create-saving").show();

			var data = {
				action: "create_tab",
				tab_container_id: tab_container_id,
				title: $("#new-tab-container-title").val()
			};
			$.post(ajaxurl, data, function(response){
				$("#wptabs-create-submit").removeAttr("disabled");
				$("#wptabs-create-saving").fadeOut();
				if (response && response.status == 200 && response.tab){
					tab_container_view.collection.add({
						id: response.tab.id,
						title: response.tab.title
					});
				} else {
					alert("An error occured, and the new tab was not created.. Please try again..");
				}
			}, "json");
		});

	});

}(jQuery, window, document));