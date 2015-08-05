(function($) {
	
var Node = Node || {
  ELEMENT_NODE: 1,
  ATTRIBUTE_NODE: 2,
  TEXT_NODE: 3
};

$.scrollWindowTo = function(pos, duration, cb) {
  if (duration == null) {
    duration = 0;
  }
  if (pos === $(window).scrollTop()) {
    $(window).trigger('scroll');
    if (typeof cb === "function") {
      cb();
    }
    return;
  }
  return $('html, body').animate({
    scrollTop: pos
  }, duration, function() {
    return typeof cb === "function" ? cb() : void 0;
  });
};
	
	
/**
 * Main source
 */

;(function(factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['underscore', 'backbone'], factory);
    } else {
        // globals
        factory(_, Backbone);
    }
}(function(_, Backbone) {
    
    /**
     * Takes a nested object and returns a shallow object keyed with the path names
     * e.g. { "level1.level2": "value" }
     *
     * @param  {Object}      Nested object e.g. { level1: { level2: 'value' } }
     * @return {Object}      Shallow object with path names e.g. { 'level1.level2': 'value' }
     */
    function objToPaths(obj) {
        var ret = {},
            separator = DeepModel.keyPathSeparator;

        for (var key in obj) {
            var val = obj[key];

            if (val && val.constructor === Object && !_.isEmpty(val)) {
                //Recursion for embedded objects
                var obj2 = objToPaths(val);

                for (var key2 in obj2) {
                    var val2 = obj2[key2];

                    ret[key + separator + key2] = val2;
                }
            } else {
                ret[key] = val;
            }
        }

        return ret;
    }

    /**
     * @param {Object}  Object to fetch attribute from
     * @param {String}  Object path e.g. 'user.name'
     * @return {Mixed}
     */
    function getNested(obj, path, return_exists) {
        var separator = DeepModel.keyPathSeparator;

        var fields = path.split(separator);
        var result = obj;
        return_exists || (return_exists === false);
        for (var i = 0, n = fields.length; i < n; i++) {
            if (return_exists && !_.has(result, fields[i])) {
                return false;
            }
            result = result[fields[i]];

            if (result == null && i < n - 1) {
                result = {};
            }
            
            if (typeof result === 'undefined') {
                if (return_exists)
                {
                    return true;
                }
                return result;
            }
        }
        if (return_exists)
        {
            return true;
        }
        return result;
    }

    /**
     * @param {Object} obj                Object to fetch attribute from
     * @param {String} path               Object path e.g. 'user.name'
     * @param {Object} [options]          Options
     * @param {Boolean} [options.unset]   Whether to delete the value
     * @param {Mixed}                     Value to set
     */
    function setNested(obj, path, val, options) {
        options = options || {};

        var separator = DeepModel.keyPathSeparator;

        var fields = path.split(separator);
        var result = obj;
        for (var i = 0, n = fields.length; i < n && result !== undefined ; i++) {
            var field = fields[i];

            //If the last in the path, set the value
            if (i === n - 1) {
                options.unset ? delete result[field] : result[field] = val;
            } else {
                //Create the child object if it doesn't exist, or isn't an object
                if (typeof result[field] === 'undefined' || ! _.isObject(result[field])) {
                    result[field] = {};
                }

                //Move onto the next part of the path
                result = result[field];
            }
        }
    }

    function deleteNested(obj, path) {
      setNested(obj, path, null, { unset: true });
    }

    var DeepModel = Backbone.Model.extend({

        // Override constructor
        // Support having nested defaults by using _.deepExtend instead of _.extend
        constructor: function(attributes, options) {
            var defaults;
            var attrs = attributes || {};
            this.cid = _.uniqueId('c');
            this.attributes = {};
            if (options && options.collection) this.collection = options.collection;
            if (options && options.parse) attrs = this.parse(attrs, options) || {};
            if (defaults = _.result(this, 'defaults')) {
                //<custom code>
                // Replaced the call to _.defaults with _.deepExtend.
                attrs = _.deepExtend({}, defaults, attrs);
                //</custom code>
            }
            this.set(attrs, options);
            this.changed = {};
            this.initialize.apply(this, arguments);
        },

        // Return a copy of the model's `attributes` object.
        toJSON: function(options) {
          return _.deepClone(this.attributes);
        },

        // Override get
        // Supports nested attributes via the syntax 'obj.attr' e.g. 'author.user.name'
        get: function(attr) {
            return getNested(this.attributes, attr);
        },

        // Override set
        // Supports nested attributes via the syntax 'obj.attr' e.g. 'author.user.name'
        set: function(key, val, options) {
            var attr, attrs, unset, changes, silent, changing, prev, current;
            if (key == null) return this;
            
            // Handle both `"key", value` and `{key: value}` -style arguments.
            if (typeof key === 'object') {
              attrs = key;
              options = val || {};
            } else {
              (attrs = {})[key] = val;
            }

            options || (options = {});
            
            // Run validation.
            if (!this._validate(attrs, options)) return false;

            // Extract attributes and options.
            unset           = options.unset;
            silent          = options.silent;
            changes         = [];
            changing        = this._changing;
            this._changing  = true;

            if (!changing) {
              this._previousAttributes = _.deepClone(this.attributes); //<custom>: Replaced _.clone with _.deepClone
              this.changed = {};
            }
            current = this.attributes, prev = this._previousAttributes;

            // Check for changes of `id`.
            if (this.idAttribute in attrs) this.id = attrs[this.idAttribute];

            //<custom code>
            attrs = objToPaths(attrs);
            //</custom code>

            // For each `set` attribute, update or delete the current value.
            for (attr in attrs) {
              val = attrs[attr];

              //<custom code>: Using getNested, setNested and deleteNested
              if (!_.isEqual(getNested(current, attr), val)) changes.push(attr);
              if (!_.isEqual(getNested(prev, attr), val)) {
                setNested(this.changed, attr, val);
              } else {
                deleteNested(this.changed, attr);
              }
              unset ? deleteNested(current, attr) : setNested(current, attr, val);
              //</custom code>
            }

            // Trigger all relevant attribute changes.
            if (!silent) {
              if (changes.length) this._pending = true;

              //<custom code>
              var separator = DeepModel.keyPathSeparator;

              for (var i = 0, l = changes.length; i < l; i++) {
                var key = changes[i];

                this.trigger('change:' + key, this, getNested(current, key), options);

                var fields = key.split(separator);

                //Trigger change events for parent keys with wildcard (*) notation
                for(var n = fields.length - 1; n > 0; n--) {
                  var parentKey = _.first(fields, n).join(separator),
                      wildcardKey = parentKey + separator + '*';

                  this.trigger('change:' + wildcardKey, this, getNested(current, parentKey), options);
                }
                //</custom code>
              }
            }

            if (changing) return this;
            if (!silent) {
              while (this._pending) {
                this._pending = false;
                this.trigger('change', this, options);
              }
            }
            this._pending = false;
            this._changing = false;
            return this;
        },

        // Clear all attributes on the model, firing `"change"` unless you choose
        // to silence it.
        clear: function(options) {
          var attrs = {};
          var shallowAttributes = objToPaths(this.attributes);
          for (var key in shallowAttributes) attrs[key] = void 0;
          return this.set(attrs, _.extend({}, options, {unset: true}));
        },

        // Determine if the model has changed since the last `"change"` event.
        // If you specify an attribute name, determine if that attribute has changed.
        hasChanged: function(attr) {
          if (attr == null) return !_.isEmpty(this.changed);
          return getNested(this.changed, attr) !== undefined;
        },

        // Return an object containing all the attributes that have changed, or
        // false if there are no changed attributes. Useful for determining what
        // parts of a view need to be updated and/or what attributes need to be
        // persisted to the server. Unset attributes will be set to undefined.
        // You can also pass an attributes object to diff against the model,
        // determining if there *would be* a change.
        changedAttributes: function(diff) {
          //<custom code>: objToPaths
          if (!diff) return this.hasChanged() ? objToPaths(this.changed) : false;
          //</custom code>

          var old = this._changing ? this._previousAttributes : this.attributes;
          
          //<custom code>
          diff = objToPaths(diff);
          old = objToPaths(old);
          //</custom code>

          var val, changed = false;
          for (var attr in diff) {
            if (_.isEqual(old[attr], (val = diff[attr]))) continue;
            (changed || (changed = {}))[attr] = val;
          }
          return changed;
        },

        // Get the previous value of an attribute, recorded at the time the last
        // `"change"` event was fired.
        previous: function(attr) {
          if (attr == null || !this._previousAttributes) return null;

          //<custom code>
          return getNested(this._previousAttributes, attr);
          //</custom code>
        },

        // Get all of the attributes of the model at the time of the previous
        // `"change"` event.
        previousAttributes: function() {
          //<custom code>
          return _.deepClone(this._previousAttributes);
          //</custom code>
        }
    });


    //Config; override in your app to customise
    DeepModel.keyPathSeparator = '.';


    //Exports
    Backbone.DeepModel = DeepModel;

    //For use in NodeJS
    if (typeof module != 'undefined') module.exports = DeepModel;
    
    return Backbone;

}));	
	
	
// FORM BUILDER CORE //
	
(function() {
  rivets.binders.input = {
    publishes: true,
    routine: rivets.binders.value.routine,
    bind: function(el) {
      return $(el).bind('input.rivets', this.publish);
    },
    unbind: function(el) {
      return $(el).unbind('input.rivets');
    }
  };

  rivets.configure({
    prefix: "rv",
    adapter: {
      subscribe: function(obj, keypath, callback) {
        callback.wrapped = function(m, v) {
          return callback(v);
        };
        return obj.on('change:' + keypath, callback.wrapped);
      },
      unsubscribe: function(obj, keypath, callback) {
        return obj.off('change:' + keypath, callback.wrapped);
      },
      read: function(obj, keypath) {
        if (keypath === "cid") {
          return obj.cid;
        }
        return obj.get(keypath);
      },
      publish: function(obj, keypath, value) {
        if (obj.cid) {
          return obj.set(keypath, value);
        } else {
          return obj[keypath] = value;
        }
      }
    }
  });

}).call(this);

(function() {
  var BuilderView, EditFieldView, Formbuilder, FormbuilderCollection, FormbuilderModel, ViewFieldView, _ref, _ref1, _ref2, _ref3, _ref4,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  FormbuilderModel = (function(_super) {
    __extends(FormbuilderModel, _super);

    function FormbuilderModel() {
      _ref = FormbuilderModel.__super__.constructor.apply(this, arguments);
      return _ref;
    }

    FormbuilderModel.prototype.sync = function() {};

    FormbuilderModel.prototype.indexInDOM = function() {
      var $wrapper,
        _this = this;
      $wrapper = $(".fb-field-wrapper").filter((function(_, el) {
        return $(el).data('cid') === _this.cid;
      }));
      return $(".fb-field-wrapper").index($wrapper);
    };

    FormbuilderModel.prototype.is_input = function() {
      return Formbuilder.inputFields[this.get(Formbuilder.options.mappings.FIELD_TYPE)] != null;
    };

    return FormbuilderModel;

  })(Backbone.DeepModel);

  FormbuilderCollection = (function(_super) {
    __extends(FormbuilderCollection, _super);

    function FormbuilderCollection() {
      _ref1 = FormbuilderCollection.__super__.constructor.apply(this, arguments);
      return _ref1;
    }

    FormbuilderCollection.prototype.initialize = function() {
      return this.on('add', this.copyCidToModel);
    };

    FormbuilderCollection.prototype.model = FormbuilderModel;

    FormbuilderCollection.prototype.comparator = function(model) {
      return model.indexInDOM();
    };

    FormbuilderCollection.prototype.copyCidToModel = function(model) {
      return model.attributes.cid = model.cid;
    };

    return FormbuilderCollection;

  })(Backbone.Collection);

  ViewFieldView = (function(_super) {
    __extends(ViewFieldView, _super);

    function ViewFieldView() {
      _ref2 = ViewFieldView.__super__.constructor.apply(this, arguments);
      return _ref2;
    }

    ViewFieldView.prototype.className = "fb-field-wrapper";

    ViewFieldView.prototype.events = {
      'click .subtemplate-wrapper': 'focusEditView',
      'click .js-duplicate': 'duplicate',
      'click .js-clear': 'clear'
    };

    ViewFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      this.listenTo(this.model, "change", this.render);
      return this.listenTo(this.model, "destroy", this.remove);
    };

    ViewFieldView.prototype.render = function() {
      this.$el.addClass('response-field-' + this.model.get(Formbuilder.options.mappings.FIELD_TYPE)).data('cid', this.model.cid).html(Formbuilder.templates["view/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      return this;
    };

    ViewFieldView.prototype.focusEditView = function() {
      return this.parentView.createAndShowEditView(this.model);
    };

    ViewFieldView.prototype.clear = function(e) {
      var cb, x,
        _this = this;
      e.preventDefault();
      e.stopPropagation();
      cb = function() {
        _this.parentView.handleFormUpdate();
        return _this.model.destroy();
      };
      x = Formbuilder.options.CLEAR_FIELD_CONFIRM;
      switch (typeof x) {
        case 'string':
          if (confirm(x)) {
            return cb();
          }
          break;
        case 'function':
          return x(cb);
        default:
          return cb();
      }
    };

    ViewFieldView.prototype.duplicate = function() {
      var attrs;
      attrs = _.clone(this.model.attributes);
      delete attrs['id'];
      attrs['label'] += ' Copy';
      return this.parentView.createField(attrs, {
        position: this.model.indexInDOM() + 1
      });
    };

    return ViewFieldView;

  })(Backbone.View);

  EditFieldView = (function(_super) {
    __extends(EditFieldView, _super);

    function EditFieldView() {
      _ref3 = EditFieldView.__super__.constructor.apply(this, arguments);
      return _ref3;
    }

    EditFieldView.prototype.className = "edit-response-field";

    EditFieldView.prototype.events = {
      'click .js-add-option': 'addOption',
      'click .js-remove-option': 'removeOption',
      'click .js-default-updated': 'defaultUpdated',
      'input .option-label-input': 'forceRender'
    };

    EditFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      return this.listenTo(this.model, "destroy", this.remove);
    };

    EditFieldView.prototype.render = function() {
      this.$el.html(Formbuilder.templates["edit/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      rivets.bind(this.$el, {
        model: this.model
      });
      return this;
    };

    EditFieldView.prototype.remove = function() {
      this.parentView.editView = void 0;
      this.parentView.$el.find("[data-target=\"#addField\"]").click();
      return EditFieldView.__super__.remove.apply(this, arguments);
    };

    EditFieldView.prototype.addOption = function(e) {
      var $el, i, newOption, options;
      $el = $(e.currentTarget);
      i = this.$el.find('.option').index($el.closest('.option'));
      options = this.model.get(Formbuilder.options.mappings.OPTIONS) || [];
      newOption = {
        label: "",
        checked: false
      };
      if (i > -1) {
        options.splice(i + 1, 0, newOption);
      } else {
        options.push(newOption);
      }
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.removeOption = function(e) {
      var $el, index, options;
      $el = $(e.currentTarget);
      index = this.$el.find(".js-remove-option").index($el);
      options = this.model.get(Formbuilder.options.mappings.OPTIONS);
      options.splice(index, 1);
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.defaultUpdated = function(e) {
      var $el;
      $el = $(e.currentTarget);
      if (this.model.get(Formbuilder.options.mappings.FIELD_TYPE) !== 'checkboxes') {
        this.$el.find(".js-default-updated").not($el).attr('checked', false).trigger('change');
      }
      return this.forceRender();
    };

    EditFieldView.prototype.forceRender = function() {
      return this.model.trigger('change');
    };

    return EditFieldView;

  })(Backbone.View);

  BuilderView = (function(_super) {
    __extends(BuilderView, _super);

    function BuilderView() {
      _ref4 = BuilderView.__super__.constructor.apply(this, arguments);
      return _ref4;
    }

    BuilderView.prototype.SUBVIEWS = [];

    BuilderView.prototype.events = {
      'click .js-save-form': 'saveForm',
      'click .fb-tabs a': 'showTab',
      'click .fb-add-field-types a': 'addField',
      'mouseover .fb-add-field-types': 'lockLeftWrapper',
      'mouseout .fb-add-field-types': 'unlockLeftWrapper'
    };

    BuilderView.prototype.initialize = function(options) {
      var selector;
      selector = options.selector, this.formBuilder = options.formBuilder, this.bootstrapData = options.bootstrapData;
      if (selector != null) {
        this.setElement($(selector));
      }
      this.collection = new FormbuilderCollection;
      this.collection.bind('add', this.addOne, this);
      this.collection.bind('reset', this.reset, this);
      this.collection.bind('change', this.handleFormUpdate, this);
      this.collection.bind('destroy add reset', this.hideShowNoResponseFields, this);
      this.collection.bind('destroy', this.ensureEditViewScrolled, this);
      this.render();
      this.collection.reset(this.bootstrapData);
      return this.bindSaveEvent();
    };

    BuilderView.prototype.bindSaveEvent = function() {
      var _this = this;
      this.formSaved = true;
      this.saveFormButton = this.$el.find(".js-save-form");
      this.saveFormButton.attr('disabled', true).text(Formbuilder.options.dict.ALL_CHANGES_SAVED);
      if (!!Formbuilder.options.AUTOSAVE) {
        setInterval(function() {
          return _this.saveForm.call(_this);
        }, 1000);
      }
      return $(window).bind('beforeunload', function() {
        if (_this.formSaved) {
          return void 0;
        } else {
          return Formbuilder.options.dict.UNSAVED_CHANGES;
        }
      });
    };

    BuilderView.prototype.reset = function() {
      this.$responseFields.html('');
      return this.addAll();
    };

    BuilderView.prototype.render = function() {
      var subview, _i, _len, _ref5;
      this.$el.html(Formbuilder.templates['page']());
      this.$fbLeft = this.$el.find('.fb-left');
      this.$responseFields = this.$el.find('.fb-response-fields');
      this.bindWindowScrollEvent();
      this.hideShowNoResponseFields();
      _ref5 = this.SUBVIEWS;
      for (_i = 0, _len = _ref5.length; _i < _len; _i++) {
        subview = _ref5[_i];
        new subview({
          parentView: this
        }).render();
      }
	  
	  jQuery("#fbloader").css("display", "none").removeClass("tbloader");
	  jQuery(".fb-main").fadeIn(1000); // @since 1.0.25
	 
      return this;
    };

    BuilderView.prototype.bindWindowScrollEvent = function() {
      var _this = this;
      return $(window).on('scroll', function() {
        var maxMargin, newMargin;
        if (_this.$fbLeft.data('locked') === true) {
          return;
        }
        newMargin = Math.max(0, $(window).scrollTop() - _this.$el.offset().top+23);
        maxMargin = _this.$responseFields.height();
        return _this.$fbLeft.css({
          'margin-top': Math.min(maxMargin, newMargin)
        });
      });
    };

    BuilderView.prototype.showTab = function(e) {
      var $el, first_model, target;
      $el = $(e.currentTarget);
      target = $el.data('target');
      $el.closest('li').addClass('active').siblings('li').removeClass('active');
      $(target).addClass('active').siblings('.fb-tab-pane').removeClass('active');
      if (target !== '#editField') {
        this.unlockLeftWrapper();
      }
      if (target === '#editField' && !this.editView && (first_model = this.collection.models[0])) {
        return this.createAndShowEditView(first_model);
      }
    };

    BuilderView.prototype.addOne = function(responseField, _, options) {
      var $replacePosition, view;
      view = new ViewFieldView({
        model: responseField,
        parentView: this
      });
      if (options.$replaceEl != null) {
        return options.$replaceEl.replaceWith(view.render().el);
      } else if ((options.position == null) || options.position === -1) {
        return this.$responseFields.append(view.render().el);
      } else if (options.position === 0) {
        return this.$responseFields.prepend(view.render().el);
      } else if (($replacePosition = this.$responseFields.find(".fb-field-wrapper").eq(options.position))[0]) {
        return $replacePosition.before(view.render().el);
      } else {
        return this.$responseFields.append(view.render().el);
      }
    };

    BuilderView.prototype.setSortable = function() {
      var _this = this;
      if (this.$responseFields.hasClass('ui-sortable')) {
        this.$responseFields.sortable('destroy');
      }
      this.$responseFields.sortable({
        forcePlaceholderSize: true,
        placeholder: 'sortable-placeholder',
        stop: function(e, ui) {
          var rf;
          if (ui.item.data('field-type')) {
            rf = _this.collection.create(Formbuilder.helpers.defaultFieldAttrs(ui.item.data('field-type')), {
              $replaceEl: ui.item
            });
            _this.createAndShowEditView(rf);
          }
          _this.handleFormUpdate();
          return true;
        },
        update: function(e, ui) {
          if (!ui.item.data('field-type')) {
            return _this.ensureEditViewScrolled();
          }
        }
      });
      //return this.setDraggable();
    };

    BuilderView.prototype.setDraggable = function() {
      var $addFieldButtons,
        _this = this;
      $addFieldButtons = this.$el.find("[data-field-type]");
      return $addFieldButtons.draggable({
        connectToSortable: this.$responseFields,
        helper: function() {
          var $helper;
          $helper = $("<div class='response-field-draggable-helper' />");
          $helper.css({
            width: _this.$responseFields.width(),
            height: '80px'
          });
          return $helper;
        }
      });
    };

    BuilderView.prototype.addAll = function() {
      this.collection.each(this.addOne, this);
      return this.setSortable();
    };

    BuilderView.prototype.hideShowNoResponseFields = function() {
      return this.$el.find(".fb-no-response-fields")[this.collection.length > 0 ? 'hide' : 'show']();
    };

    BuilderView.prototype.addField = function(e) {
      var field_type;
      field_type = $(e.currentTarget).data('field-type');
      return this.createField(Formbuilder.helpers.defaultFieldAttrs(field_type));
    };

    BuilderView.prototype.createField = function(attrs, options) {
      var rf;
      rf = this.collection.create(attrs, options);
      this.createAndShowEditView(rf);
      return this.handleFormUpdate();
    };

    BuilderView.prototype.createAndShowEditView = function(model) {
      var $newEditEl, $responseFieldEl;
      $responseFieldEl = this.$el.find(".fb-field-wrapper").filter(function() {
        return $(this).data('cid') === model.cid;
      });
      $responseFieldEl.addClass('editing').siblings('.fb-field-wrapper').removeClass('editing');
      if (this.editView) {
        if (this.editView.model.cid === model.cid) {
          this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
          this.scrollLeftWrapper($responseFieldEl);
          return;
        }
        this.editView.remove();
      }
      this.editView = new EditFieldView({
        model: model,
        parentView: this
      });
      $newEditEl = this.editView.render().$el;
      this.$el.find(".fb-edit-field-wrapper").html($newEditEl);
      this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
      this.scrollLeftWrapper($responseFieldEl);
      return this;
    };

    BuilderView.prototype.ensureEditViewScrolled = function() {
      if (!this.editView) {
        return;
      }
      return this.scrollLeftWrapper($(".fb-field-wrapper.editing"));
    };

    BuilderView.prototype.scrollLeftWrapper = function($responseFieldEl) {
      var _this = this;
      this.unlockLeftWrapper();
      if (!$responseFieldEl[0]) {
        return;
      }
      return $.scrollWindowTo((this.$el.offset().top + $responseFieldEl.offset().top) - this.$responseFields.offset().top-10, 1000, function() {
        return _this.lockLeftWrapper();
      });
    };

    BuilderView.prototype.lockLeftWrapper = function() {
      return this.$fbLeft.data('locked', true);
    };

    BuilderView.prototype.unlockLeftWrapper = function() {
      return this.$fbLeft.data('locked', false);
    };

    BuilderView.prototype.handleFormUpdate = function() {
      if (this.updatingBatch) {
        return;
      }
      this.formSaved = false;
      return this.saveFormButton.removeAttr('disabled').text(Formbuilder.options.dict.SAVE_FORM);
    };

    BuilderView.prototype.saveForm = function(e) {
      var payload;
      if (this.formSaved) {
        return;
      }
      this.formSaved = true;
      this.saveFormButton.attr('disabled', true).text(Formbuilder.options.dict.ALL_CHANGES_SAVED);
      this.collection.sort();
	  
	  
      payload = JSON.stringify({
        fields: this.collection.toJSON()
      });
	  
	 var fieldsJson = JSON.parse(payload).fields;
	 var t = 0;

	jQuery.each(fieldsJson, function(i, val) {
   		if(val.field_type == "attachment"){
			t = t + 1;
  		}
	});
	
	if(t > 0){
		$( "#ecf-field-attachment" ).prop( "disabled", true ).attr('disabled','disabled');
		}
		else {
			$( "#ecf-field-attachment" ).prop( "disabled", false ).removeAttr('disabled');
			} 

	  
      if (Formbuilder.options.HTTP_ENDPOINT) {
        this.doAjaxSave(payload);
      }
      return this.formBuilder.trigger('save', payload);
    };

    BuilderView.prototype.doAjaxSave = function(payload) {
      var _this = this;
      return $.ajax({
        url: Formbuilder.options.HTTP_ENDPOINT,
        type: Formbuilder.options.HTTP_METHOD,
        data: payload,
        contentType: "application/json",
        success: function(data) {
          var datum, _i, _len, _ref5;
          _this.updatingBatch = true;
          for (_i = 0, _len = data.length; _i < _len; _i++) {
            datum = data[_i];
            if ((_ref5 = _this.collection.get(datum.cid)) != null) {
              _ref5.set({
                id: datum.id
              });
            }
            _this.collection.trigger('sync');
          }
          return _this.updatingBatch = void 0;
        }
      });
    };

    return BuilderView;

  })(Backbone.View);

  Formbuilder = (function() {
    Formbuilder.helpers = {
      defaultFieldAttrs: function(field_type) {
        var attrs, _base;
        attrs = {};
        attrs[Formbuilder.options.mappings.LABEL] = 'Untitled';
        attrs[Formbuilder.options.mappings.FIELD_TYPE] = field_type;
        attrs[Formbuilder.options.mappings.REQUIRED] = true;
        attrs['field_options'] = {};
        return (typeof (_base = Formbuilder.fields[field_type]).defaultAttributes === "function" ? _base.defaultAttributes(attrs) : void 0) || attrs;
      },
      simple_format: function(x) {
        return x != null ? x.replace(/\n/g, '<br />') : void 0;
      }
    };

    Formbuilder.options = {
      BUTTON_CLASS: 'fb-button',
      HTTP_ENDPOINT: '',
      HTTP_METHOD: 'POST',
      AUTOSAVE: true,
      CLEAR_FIELD_CONFIRM: false,
      mappings: {
        SIZE: 'field_options.size',
        UNITS: 'field_options.units',
		ICONS: 'icons',
		ICONPOS: 'iconpos',
		PLACEHOLDER: 'placeholder',
		PHONEMASK: 'phonemask',
		DATEFORMAT: 'dateformat',
		DATESTART: 'datestart',		
		DATEFINISH: 'datefinish',
		SLIDEMIN: 'slidemin',
		SLIDEMAX: 'slidemax',		
		SLIDESTEP: 'slidestep',
		SLIDETYPE: 'slidetype',
        LABEL: 'label',
        FIELD_TYPE: 'field_type',
        REQUIRED: 'required',
        ICONCSTM: 'iconcstm',
        ATTACHNOTE: 'attachnote',
        ADMIN_ONLY: 'admin_only',
        OPTIONS: 'field_options.options',
        DESCRIPTION: 'field_options.description',
        INCLUDE_OTHER: 'field_options.include_other_option',
        INCLUDE_BLANK: 'field_options.include_blank_option',
        INTEGER_ONLY: 'field_options.integer_only',
        MIN: 'field_options.min',
        MAX: 'field_options.max',
        MINLENGTH: 'field_options.minlength',
        MAXLENGTH: 'field_options.maxlength',
        LENGTH_UNITS: 'field_options.min_max_length_units'
      },
      dict: {
        ALL_CHANGES_SAVED: 'All changes saved',
        SAVE_FORM: 'Save form',
        UNSAVED_CHANGES: 'You have unsaved changes. If you leave this page, you will lose those changes!'
      }
    };

    Formbuilder.fields = {};

    Formbuilder.inputFields = {};

    Formbuilder.nonInputFields = {};

    Formbuilder.registerField = function(name, opts) {
      var x, _i, _len, _ref5;
      _ref5 = ['view', 'edit'];
      for (_i = 0, _len = _ref5.length; _i < _len; _i++) {
        x = _ref5[_i];
        opts[x] = _.template(opts[x]);
      }
      opts.field_type = name;
      Formbuilder.fields[name] = opts;
      if (opts.type === 'non_input') {
        return Formbuilder.nonInputFields[name] = opts;
      } else {
        return Formbuilder.inputFields[name] = opts;
      }
    };

    function Formbuilder(opts) {
      var args;
      if (opts == null) {
        opts = {};
      }
      _.extend(this, Backbone.Events);
      args = _.extend(opts, {
        formBuilder: this
      });
      this.mainView = new BuilderView(args);
    }

    return Formbuilder;

  })();

  window.Formbuilder = Formbuilder;

  if (typeof module !== "undefined" && module !== null) {
    module.exports = Formbuilder;
  } else {
    window.Formbuilder = Formbuilder;
  }

}).call(this);

// ELEMENT


(function() {
  Formbuilder.registerField('department', {
    order: 31,
    view: "<select>\n  <% if (rf.get(Formbuilder.options.mappings.INCLUDE_BLANK)) { %>\n    <option value=''></option>\n  <% } %>\n\n  <% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n    <option <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'selected' %>>\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </option>\n  <% } %>\n</select><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/optionsdept']({ includeBlank: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-users\"></span></span> Department",
    defaultAttributes: function(attrs) {
	  attrs.label = 'Department';	
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }
      ];
      attrs.field_options.include_blank_option = false;
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('dropdown', {
    order: 24,
    view: "<select>\n  <% if (rf.get(Formbuilder.options.mappings.INCLUDE_BLANK)) { %>\n    <option value=''></option>\n  <% } %>\n\n  <% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n    <option <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'selected' %>>\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </option>\n  <% } %>\n</select><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeBlank: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-caret-down\"></span></span> Dropdown",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      attrs.field_options.include_blank_option = false;
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('checkboxes', {
    order: 26,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='checkbox' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='checkbox' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-square-o\"></span></span> Checkboxes",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('radio', {
    order: 15,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='radio' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='radio' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-circle-o\"></span></span> Radios",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('text', {
    order: 4,
    view: "<input placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' />",
    edit: "<%= Formbuilder.templates['edit/icon']() %>",
    addButton: "<span class='symbol'><span class='fa fa-font'></span></span> Text",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.icons = 'none';
	  attrs.iconpos = 'prepend';	  
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('phone', {
    order: 30,
    view: "<input placeholder='<%= rf.get(Formbuilder.options.mappings.PHONEMASK) %>' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' /><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/phone']() %>",
    addButton: "<span class='symbol'><span class='fa fa-phone'></span></span> Phone Number",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.icons = 'fa-phone';
	  attrs.iconpos = 'prepend';	
	  attrs.label = 'Phone Number';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('name', {
    order: 0,
    view: "<input placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' />",
    edit: "<%= Formbuilder.templates['edit/icon']() %>",
    addButton: "<span class='symbol'><span class='fa fa-user'></span></span> Name",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.icons = 'fa-user';
	  attrs.iconpos = 'prepend';
      attrs.label = 'Name';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('rating', {
    order: 27,
    view: "<span class='rating-view'></span><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/rating']() %>",
    addButton: "<span class='symbol'><span class='fa fa-star'></span></span> Five Star Rating",
    defaultAttributes: function(attrs) {
	  attrs.label = 'Rating';
	  attrs.icons = 'fa-star';
      attrs.field_options.size = 'medium';	  
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('date', {
    order: 28,
    view: "<input placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-frmt='<%= rf.get(Formbuilder.options.mappings.DATEFORMAT) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' /><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/date']() %>",
    addButton: "<span class='symbol'><span class='fa fa-calendar'></span></span> Date",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.icons = 'fa-calendar';
	  attrs.iconpos = 'prepend';
      attrs.label = 'Date';
	  attrs.dateformat = 'd MM, y';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('daterange', {
    order: 29,
    view: "<input placeholder='<%= rf.get(Formbuilder.options.mappings.DATESTART) %>' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-frmt='<%= rf.get(Formbuilder.options.mappings.DATEFORMAT) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' /><input placeholder='<%= rf.get(Formbuilder.options.mappings.DATEFINISH) %>' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-frmt='<%= rf.get(Formbuilder.options.mappings.DATEFORMAT) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' /><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/daterange']() %>",
    addButton: "<span class='symbol'><span class='fa fa-calendar'></span></span> Date Range",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
	  attrs.icons = 'fa-calendar';
	  attrs.iconpos = 'prepend';
      attrs.label = 'Select Date Range';
	  attrs.datestart = 'Start Date';	
	  attrs.datefinish = 'Expected Finish Date';
	  attrs.dateformat = 'd MM, yy';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('slider', {
    order: 36,
    view: "<input class='ecf-slide' /><br /><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/slider']() %>",
    addButton: "<span class='symbol'><span class='fa fa-exchange'></span></span> Slider",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
      attrs.label = 'Slider';
	  attrs.slidemin = '0';	
	  attrs.slidemax = '100';
	  attrs.slidestep = '10';
	  attrs.slidetype = '%';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('attachment', {
    order: 111,
    view: "<label class='input input-file'><div class='button'><input id='file' type='file'></input>Browse</div><input class='isattach' type='text' readonly></label><div style='padding-top: 12px; padding-bottom: 7px; font-size: 12px; color: #F40043;'><i>This feature only available in Pro Version</i></div>",
    edit: "<%= Formbuilder.templates['edit/attach']() %>",
    addButton: "<span class='symbol'><span class='fa fa-paperclip'></span></span> Attachment",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
      attrs.label = 'Attachment';
	  attrs.field_options.description = 'You can attach the following file formats: gif, png, jpg, jpeg, tiff, bmp, ai, pdf, doc, docx, xls, zip, rar, mp3, wav, ppt';
	  attrs.required = false;
	  attrs.attachnote = true;
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('email', {
    order: 3,
    view: "<input placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' />",
    edit: "<%= Formbuilder.templates['edit/icon']() %>",
    addButton: "<span class='symbol'><span class='fa fa-envelope-o'></span></span> Email",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
      attrs.label = 'Email';
	  attrs.icons = 'fa-envelope-o';
	  attrs.iconpos = 'prepend';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('website', {
    order: 35,
    view: "<input placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' placeholder='http://' type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>' />",
    edit: "<%= Formbuilder.templates['edit/icon']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-link\"></span></span> Website",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'medium';
      attrs.icons = 'fa-link';
	  attrs.iconpos = 'prepend';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('paragraph', {
    order: 5,
    view: "<textarea placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>'></textarea>",
    edit: "<%= Formbuilder.templates['edit/icon']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-comment\"></span></span> Textarea",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'large';
      attrs.icons = 'fa-comment';
	  attrs.iconpos = 'prepend';
      return attrs;
    }
  });

}).call(this);


(function() {
  Formbuilder.registerField('message', {
    order: 5,
    view: "<textarea placeholder='<%= rf.get(Formbuilder.options.mappings.PLACEHOLDER) %>' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' data-icon='<%= rf.get(Formbuilder.options.mappings.ICONS) %>'></textarea>",
    edit: "<%= Formbuilder.templates['edit/icon']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-comment\"></span></span> Message",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'large';
      attrs.label = 'Message';
      attrs.icons = 'fa-comment';
	  attrs.iconpos = 'prepend';
      return attrs;
    }
  });

}).call(this);



this["Formbuilder"] = this["Formbuilder"] || {};
this["Formbuilder"]["templates"] = this["Formbuilder"]["templates"] || {};

this["Formbuilder"]["templates"]["edit/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['edit/common']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_header"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-field-label\'>\n  <span data-rv-text="model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'"></span>\n  <code class=\'field-type\' data-rv-text=\'model.' +
((__t = ( Formbuilder.options.mappings.FIELD_TYPE )) == null ? '' : __t) +
'\'></code>\n  <span class=\'fa fa-arrow-right pull-right\'></span>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/checkboxes"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.REQUIRED )) == null ? '' : __t) +
'\' />\n  Required\n</label>\n<!-- label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.ADMIN_ONLY )) == null ? '' : __t) +
'\' />\n  Admin only\n</label -->';

}
return __p
};

this["Formbuilder"]["templates"]["edit/common"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Label</div>\n\n<div class=\'fb-common-wrapper\'>\n  <div class=\'fb-label-description\'>\n    ' +
((__t = ( Formbuilder.templates['edit/label_description']() )) == null ? '' : __t) +
'\n  </div>\n  <div class=\'fb-common-checkboxes\'>\n    ' +
((__t = ( Formbuilder.templates['edit/checkboxes']() )) == null ? '' : __t) +
'\n  </div>\n  <div class=\'fb-clear\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/integer_only"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Integer only</div>\n<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INTEGER_ONLY )) == null ? '' : __t) +
'\' />\n  Only accept integers\n</label>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/label_description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<input type=\'text\' data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'\' />\n<textarea style="display: none;" data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.DESCRIPTION )) == null ? '' : __t) +
'\'\n  placeholder=\'Add a longer description to this field\'></textarea>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/min_max"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Minimum / Maximum</div>\n\nAbove\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MIN )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\nBelow\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAX )) == null ? '' : __t) +
'" style="width: 30px" />\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/min_max_length"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div style="display: none;" class=\'fb-edit-section-header\'>Length Limit</div><input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MINLENGTH )) == null ? '' : __t) +
'" style="width: 30px;display: none;" /><input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAXLENGTH )) == null ? '' : __t) +
'" style="width: 30px;display: none;" />\n\n&nbsp;&nbsp;\n\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.LENGTH_UNITS )) == null ? '' : __t) +
'" style="width: auto;display: none;">\n  <option value="characters">characters</option>\n  <option value="words">words</option>\n</select>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/options"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Options</div>\n\n';
 if (typeof includeBlank !== 'undefined'){ ;
__p += '\n  <label>\n    <input style="display: none;" type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_BLANK )) == null ? '' : __t) +
'\' /></label>\n';
 } ;
__p += '\n\n<div style="margin-bottom: 7px;" class=\'option\' data-rv-each-option=\'model.' +
((__t = ( Formbuilder.options.mappings.OPTIONS )) == null ? '' : __t) +
'\'>\n  <span style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">set as default</span><input type="checkbox" class=\'js-default-updated\' data-rv-checked="option:checked" />\n  <input style="margin-top:10px;" type="text" data-rv-input="option:label" class=\'option-label-input\' />\n  <a style="display: none;" class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Add Option"><i class=\'fa fa-plus-circle\'></i></a>\n  <a style="margin-bottom:10px;" class="js-remove-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Option"><i class=\'fa fa-minus-circle\'></i></a>\n</div>\n\n';
 if (typeof includeOther !== 'undefined'){ ;
__p += '\n  <label style="display: none;">\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_OTHER )) == null ? '' : __t) +
'\' />\n    Include "other"\n  </label>\n';
 } ;
__p += '\n\n<div class=\'fb-bottom-add\'>\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">Add option</a>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/size"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Size</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.SIZE )) == null ? '' : __t) +
'">\n  <option value="small">Small</option>\n  <option value="medium">Medium</option>\n  <option value="large">Large</option>\n</select>\n';

}
return __p
};


// CUSTOM OPTION

this["Formbuilder"]["templates"]["edit/phone"] = function(obj) { // @since 1.0.7.11
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Phone Format</div>\n  <input placeholder="Default - (xxx) xxx-xxx-xxx" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.PHONEMASK )) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n  <p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Custom Phone mask? Learn more <a style="text-decoration: none !important;" href="http://digitalbush.com/projects/masked-input-plugin/" target="_blank">here</a></p>\n <div class=\'fb-edit-section-header\'>Icon<span style="font-style: italic; font-size:12px; color: #F40043;">&nbsp;&nbsp; ( Pro Version )</span></div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONS )) == null ? '' : __t) + 
'">\n  <option value="none">None</option>\n  <option value="fa-user">Person</option>\n  <option value="fa-envelope-o">Envelope</option>\n  <option value="fa-asterisk">Asterisk</option>\n  <option value="fa-link">Link</option>\n  <option value="fa-star">Star</option>\n <option value="fa-users">Users</option>\n  <option value="fa-check">Check</option>\n  <option value="fa-comment">Message</option>\n <option value="fa-phone">Phone</option>\n <option value="fa-calendar">Calendar</option>\n  </select>\n  <div class=\'fb-edit-section-header-custom-icon\'></div>\n  <p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Custom Icon Class. Learn more <a style="text-decoration: none !important;" href="http://goo.gl/mjokMW" target="_blank">here</a></p>\n  <input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.ICONCSTM)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n  <div class=\'fb-edit-section-header\'>Icon Position</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONPOS )) == null ? '' : __t) + 
'">\n  <option value="prepend">Left</option>\n  <option value="append">Right</option>\n</select>\n';

}
return __p
};


this["Formbuilder"]["templates"]["edit/icon"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Placeholder</div>\n  <input placeholder="Placeholder" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.PLACEHOLDER)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n  <div class=\'fb-edit-section-header\'>Icon <span style="font-style: italic; font-size:12px; color: #F40043;">&nbsp;&nbsp; ( Pro Version )</span></div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONS )) == null ? '' : __t) + 
'">\n  <option value="none">None</option>\n  <option value="fa-user">Person</option>\n  <option value="fa-envelope-o">Envelope</option>\n  <option value="fa-asterisk">Asterisk</option>\n  <option value="fa-link">Link</option>\n  <option value="fa-users">Users</option>\n  <option value="fa-check">Check</option>\n  <option value="fa-comment">Message</option>\n  </select>\n  <div class=\'fb-edit-section-header-custom-icon\'></div>\n  <p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Custom Icon Class. Learn more <a style="text-decoration: none !important;" href="http://goo.gl/mjokMW" target="_blank">here</a></p>\n  <input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.ICONCSTM)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n  <div class=\'fb-edit-section-header\'>Icon Position</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONPOS )) == null ? '' : __t) + 
'">\n  <option value="prepend">Left</option>\n  <option value="append">Right</option>\n</select>\n';

}
return __p
};

//------------------

this["Formbuilder"]["templates"]["edit/attach"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Options</div>\n<span style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Tips below the Attachment</span><input type="checkbox" data-rv-checked="model.' +
((__t = ( Formbuilder.options.mappings.ATTACHNOTE )) == null ? '' : __t) + 
'">\n  </input>\n<div style="margin-top: 25px;"><span style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Tips text:</span><textarea style="margin-top:10px;" cols= "17" rows="6" data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.DESCRIPTION )) == null ? '' : __t) +
'\'\n  placeholder=\'You can attach the following file formats: gif, png, jpg, jpeg, tiff, bmp, ai, pdf, doc, docx, xls, zip, rar, mp3, wav, ppt\'></textarea><div>';

}
return __p
};


this["Formbuilder"]["templates"]["edit/slider"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Slider Options</div>\n <p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Slider Min</p><input style="margin-bottom:7px;" placeholder="0" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.SLIDEMIN)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n';
__p += '<p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Slider Max</p><input style="margin-bottom:7px;" placeholder="100" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.SLIDEMAX)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n';
__p += '<p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Slider Step</p><input style="margin-bottom:7px;" placeholder="0" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.SLIDESTEP)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n';
__p += '<p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Slider Type</p><input style="margin-bottom:13px;" placeholder="%" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.SLIDETYPE)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n <p style="border-top: 1px solid #DDD; margin-right:5px;font-size: 11px; color:#999; font-style: italic;padding-top:5px;">NOTE : You can change the slide step from 10 ( default ) to another value if you want to determines the size or amount of each interval or step the slider takes between the min and max. Set to 0 if you want to disable step mode ( regular slider ).</p>';
}
return __p
};


//------------------



this["Formbuilder"]["templates"]["edit/optionsdept"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div style="margin-top:40px;" class=\'fb-edit-section-header\'>Departments</div>\n\n';
 if (typeof includeBlank !== 'undefined'){ ;
__p += '\n  <label>\n    <input style="display: none;" type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_BLANK )) == null ? '' : __t) +
'\' /></label>\n';
 } ;
__p += '\n\n<div style="margin-bottom: 7px;" class=\'option\' data-rv-each-option=\'model.' +
((__t = ( Formbuilder.options.mappings.OPTIONS )) == null ? '' : __t) +
'\'>\n  <span style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">set as default</span><input type="checkbox" class=\'js-default-updated\' data-rv-checked="option:checked" />\n  <input placeholder="Department Name" style="margin-top:10px;" type="text" data-rv-input="option:label" class=\'option-label-input\' />\n  <input placeholder="Email Address" style="margin-top:10px;" type="text" data-rv-input="option:emailaddress" class=\'option-label-input\' />\n  <a style="display: none;" class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Add Option"><i class=\'fa fa-plus-circle\'></i></a>\n  <a style="margin-bottom:10px;" class="js-remove-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Option"><i class=\'fa fa-minus-circle\'></i></a>\n</div>\n\n';
 if (typeof includeOther !== 'undefined'){ ;
__p += '\n  <label>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_OTHER )) == null ? '' : __t) +
'\' />\n    Include "other"\n  </label>\n';
 } ;
__p += '\n\n<div class=\'fb-bottom-add\'>\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">Add Department</a>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/rating"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Icon<span style="font-style: italic; font-size:12px; color: #F40043;">&nbsp;&nbsp; ( Pro Version )</span></div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONS )) == null ? '' : __t) + 
'">\n  <option value="fa-star">Star A</option>\n <option value="fa-star-o">Star B</option>\n  <option value="fa-thumbs-up">Thumbs Up A</option>\n  <option value="fa-thumbs-o-up">Thumbs Up B</option>\n  <option value="fa-trophy">Trophy</option>\n  <option value="fa-asterisk">Asterisk</option>\n </select>\n  <div class=\'fb-edit-section-header-custom-icon\'></div>\n  <p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Custom Icon Class. Learn more <a style="text-decoration: none !important;" href="http://goo.gl/mjokMW" target="_blank">here</a></p>\n  <input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.ICONCSTM)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n';

}
return __p
};


this["Formbuilder"]["templates"]["edit/date"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Placeholder</div>\n  <input placeholder="Placeholder" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.PLACEHOLDER)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n <div class=\'fb-edit-section-header\'>Date Format</div>\n<select data-rv-value="model.' + ((__t = ( Formbuilder.options.mappings.DATEFORMAT )) == null ? '' : __t) + 
'">\n  <option value="d MM, yy">Default - d MM, yy</option>\n  <option value="mm/dd/yy">Normal - mm/dd/yy</option>\n  <option value="yy-mm-dd">ISO 8601 - yy-mm-dd</option>\n  <option value="d M, y">Short - d M, y</option>\n  <option value="d MM, y">Medium - d MM, y</option>\n  <option value="DD, d MM, yy">Full - DD, d MM, yy</option>\n  </select>\n <div class=\'fb-edit-section-header\'>Icon<span style="font-style: italic; font-size:12px; color: #F40043;">&nbsp;&nbsp; ( Pro Version )</span></div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONS )) == null ? '' : __t) + 
'">\n  <option value="none">None</option>\n  <option value="fa-user">Person</option>\n  <option value="fa-envelope-o">Envelope</option>\n  <option value="fa-asterisk">Asterisk</option>\n  <option value="fa-link">Link</option>\n  <option value="fa-star">Star</option>\n <option value="fa-users">Users</option>\n  <option value="fa-check">Check</option>\n  <option value="fa-comment">Message</option>\n <option value="fa-calendar">Calendar</option>\n  </select>\n  <div class=\'fb-edit-section-header-custom-icon\'></div>\n  <p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Custom Icon Class. Learn more <a style="text-decoration: none !important;" href="http://goo.gl/mjokMW" target="_blank">here</a></p>\n  <input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.ICONCSTM)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n  <div class=\'fb-edit-section-header\'>Icon Position</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONPOS )) == null ? '' : __t) + 
'">\n  <option value="prepend">Left</option>\n  <option value="append">Right</option>\n</select>\n';

}
return __p
};


this["Formbuilder"]["templates"]["edit/daterange"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Date Labels</div>\n  <input style="margin-bottom:7px;" placeholder="Start Date" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.DATESTART)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n <input placeholder="Expected Finish Date" type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.DATEFINISH)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n <div class=\'fb-edit-section-header\'>Date Format</div>\n<select data-rv-value="model.' + ((__t = ( Formbuilder.options.mappings.DATEFORMAT )) == null ? '' : __t) + 
'">\n  <option value="d MM, yy">Default - d MM, yy</option>\n  <option value="mm/dd/yy">Normal - mm/dd/yy</option>\n  <option value="yy-mm-dd">ISO 8601 - yy-mm-dd</option>\n  <option value="d M, y">Short - d M, y</option>\n  <option value="d MM, y">Medium - d MM, y</option>\n  <option value="DD, d MM, yy">Full - DD, d MM, yy</option>\n  </select>\n <div class=\'fb-edit-section-header\'>Icon<span style="font-style: italic; font-size:12px; color: #F40043;">&nbsp;&nbsp; ( Pro Version )</span></div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONS )) == null ? '' : __t) +
'">\n  <option value="none">None</option>\n  <option value="fa-user">Person</option>\n  <option value="fa-envelope-o">Envelope</option>\n  <option value="fa-asterisk">Asterisk</option>\n  <option value="fa-link">Link</option>\n  <option value="fa-star">Star</option>\n <option value="fa-users">Users</option>\n  <option value="fa-check">Check</option>\n  <option value="fa-comment">Message</option>\n <option value="fa-phone">Phone</option>\n <option value="fa-calendar">Calendar</option>\n  </select>\n  <div class=\'fb-edit-section-header-custom-icon\'></div>\n  <p style="margin-right:5px;font-size: 11px; color:#999; font-style: italic;">Custom Icon Class. Learn more <a style="text-decoration: none !important;" href="http://goo.gl/mjokMW" target="_blank">here</a></p>\n  <input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.ICONCSTM)) == null ? '' : __t) + 
'" class=\'option-label-input\' />\n  <div class=\'fb-edit-section-header\'>Icon Position</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.ICONPOS )) == null ? '' : __t) + 
'">\n  <option value="prepend">Left</option>\n  <option value="append">Right</option>\n</select>\n';

}
return __p
};



//------------------



this["Formbuilder"]["templates"]["edit/units"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Units</div>\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.UNITS )) == null ? '' : __t) +
'" />\n';

}
return __p
};

this["Formbuilder"]["templates"]["page"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['partials/save_button']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['partials/left_side']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['partials/right_side']() )) == null ? '' : __t) +
'\n<div class=\'fb-clear\'></div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/add_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-tab-pane active\' id=\'addField\'>\n  <div class=\'fb-add-field-types\'>\n    <div class=\'section\'>\n      ';
 _.each(_.sortBy(Formbuilder.inputFields, 'order'), function(f){ ;
__p += '\n        <a id="ecf-field-' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
__p += '\n    </div>\n\n    <div class=\'section\'>\n      ';
 _.each(_.sortBy(Formbuilder.nonInputFields, 'order'), function(f){ ;
__p += '\n        <a data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" id="ecf-field-' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
__p += '\n    </div>\n  </div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/edit_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-tab-pane\' id=\'editField\'>\n  <div class=\'fb-edit-field-wrapper\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/left_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-left\'>\n  <ul class=\'fb-tabs\'>\n    <li class=\'active\'><a data-target=\'#addField\'>Add new field</a></li>\n    <li><a data-target=\'#editField\'>Edit field</a></li>\n  </ul>\n\n  <div class=\'fb-tab-content\'>\n    ' +
((__t = ( Formbuilder.templates['partials/add_field']() )) == null ? '' : __t) +
'\n    ' +
((__t = ( Formbuilder.templates['partials/edit_field']() )) == null ? '' : __t) +
'\n  </div>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/right_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-right\'>\n  <div class=\'fb-no-response-fields\'>No Form Element!</div>\n  <div class=\'fb-response-fields\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/save_button"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-save-wrapper\'>\n  <button class=\'js-save-form ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'\'></button>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'subtemplate-wrapper\'>\n  <div class=\'cover\'></div>\n  ' +
((__t = ( Formbuilder.templates['view/label']({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].view({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
/*((__t = ( Formbuilder.templates['view/description']({rf: rf}) )) == null ? '' : __t) +
'\n  ' +*/
((__t = ( Formbuilder.templates['view/duplicate_remove']({rf: rf}) )) == null ? '' : __t) +
'\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '';

}
return __p
};

this["Formbuilder"]["templates"]["view/description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<span class=\'help-block\'>\n  ' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.DESCRIPTION)) )) == null ? '' : __t) +
'\n</span>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/duplicate_remove"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'actions-wrapper\'>\n  <a class="js-duplicate ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Duplicate Field"><i class=\'fa fa-plus-circle\'></i></a>\n  <a class="js-clear ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Field"><i class=\'fa fa-minus-circle\'></i></a>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/label"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<label>\n  <span>' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.LABEL)) )) == null ? '' : __t) +
'\n  ';
 if (rf.get(Formbuilder.options.mappings.REQUIRED)) { ;
__p += '\n    <abbr title=\'required\'>*</abbr>\n  ';
 } ;
__p += '\n</label>\n';

}
return __p
};
})(jQuery);