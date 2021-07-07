/* =============================================================
 * bootstrap3-typeahead.js v4.0.2
 * https://github.com/bassjobsen/Bootstrap-3-Typeahead
 * =============================================================
 * Original written by @mdo and @fat
 * =============================================================
 * Copyright 2014 Bass Jobsen @bassjobsen
 *
 * Licensed under the Apache License, Version 2.0 (the 'License');
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an 'AS IS' BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


(function (root, factory) {

  'use strict';

  // CommonJS module is defined
  if (typeof module !== 'undefined' && module.exports) {
    module.exports = factory(require('jquery'));
  }
  // AMD module is defined
  else if (typeof define === 'function' && define.amd) {
    define(['jquery'], function ($) {
      return factory ($);
    });
  } else {
    factory(root.jQuery);
  }

}(this, function ($) {

  'use strict';
  // jshint laxcomma: true


 /* TYPEAHEAD PUBLIC CLASS DEFINITION
  * ================================= */

  var Typeahead = function (element, options) {
    this.$element = $(element);
    this.options = $.extend({}, Typeahead.defaults, options);
    this.matcher = this.options.matcher || this.matcher;
    this.sorter = this.options.sorter || this.sorter;
    this.select = this.options.select || this.select;
    this.autoSelect = typeof this.options.autoSelect == 'boolean' ? this.options.autoSelect : true;
    this.highlighter = this.options.highlighter || this.highlighter;
    this.render = this.options.render || this.render;
    this.updater = this.options.updater || this.updater;
    this.displayText = this.options.displayText || this.displayText;
    this.source = this.options.source;
    this.delay = this.options.delay;
    this.$menu = $(this.options.menu);
    this.$appendTo = this.options.appendTo ? $(this.options.appendTo) : null;
    this.fitToElement = typeof this.options.fitToElement == 'boolean' ? this.options.fitToElement : false;
    this.shown = false;
    this.listen();
    this.showHintOnFocus = typeof this.options.showHintOnFocus == 'boolean' || this.options.showHintOnFocus === "all" ? this.options.showHintOnFocus : false;
    this.afterSelect = this.options.afterSelect;
    this.addItem = false;
    this.value = this.$element.val() || this.$element.text();
  };

  Typeahead.prototype = {

    constructor: Typeahead,

    select: function () {
      var val = this.$menu.find('.active').data('value');
      this.$element.data('active', val);
      if (this.autoSelect || val) {
        var newVal = this.updater(val);
        // Updater can be set to any random functions via "options" parameter in constructor above.
        // Add null check for cases when updater returns void or undefined.
        if (!newVal) {
          newVal = '';
        }
        this.$element
          .val(this.displayText(newVal) || newVal)
          .text(this.displayText(newVal) || newVal)
          .change();
        this.afterSelect(newVal);
      }
      return this.hide();
    },

    updater: function (item) {
      return item;
    },

    setSource: function (source) {
      this.source = source;
    },

    show: function () {
      var pos = $.extend({}, this.$element.position(), {
        height: this.$element[0].offsetHeight
      });

      var scrollHeight = typeof this.options.scrollHeight == 'function' ?
          this.options.scrollHeight.call() :
          this.options.scrollHeight;

      var element;
      if (this.shown) {
        element = this.$menu;
      } else if (this.$appendTo) {
        element = this.$menu.appendTo(this.$appendTo);
        this.hasSameParent = this.$appendTo.is(this.$element.parent());
      } else {
        element = this.$menu.insertAfter(this.$element);
        this.hasSameParent = true;
      }      
      
      if (!this.hasSameParent) {
          // We cannot rely on the element position, need to position relative to the window
          element.css("position", "fixed");
          var offset = this.$element.offset();
          pos.top =  offset.top;
          pos.left = offset.left;
      }
      // The rules for bootstrap are: 'dropup' in the parent and 'dropdown-menu-right' in the element.
      // Note that to get right alignment, you'll need to specify `menu` in the options to be:
      // '<ul class="typeahead dropdown-menu" role="listbox"></ul>'
      var dropup = $(element).parent().hasClass('dropup');
      var newTop = dropup ? 'auto' : (pos.top + pos.height + scrollHeight);
      var right = $(element).hasClass('dropdown-menu-right');
      var newLeft = right ? 'auto' : pos.left;
      // it seems like setting the css is a bad idea (just let Bootstrap do it), but I'll keep the old
      // logic in place except for the dropup/right-align cases.
      element.css({ top: newTop, left: newLeft }).show();

      if (this.options.fitToElement === true) {
          element.css("width", this.$element.outerWidth() + "px");
      }
    
      this.shown = true;
      return this;
    },

    hide: function () {
      this.$menu.hide();
      this.shown = false;
      return this;
    },

    lookup: function (query) {
      var items;
      if (typeof(query) != 'undefined' && query !== null) {
        this.query = query;
      } else {
        this.query = this.$element.val() || this.$element.text() || '';
      }

      if (this.query.length < this.options.minLength && !this.options.showHintOnFocus) {
        return this.shown ? this.hide() : this;
      }

      var worker = $.proxy(function () {

        if ($.isFunction(this.source)) {
          this.source(this.query, $.proxy(this.process, this));
        } else if (this.source) {
          this.process(this.source);
        }
      }, this);

      clearTimeout(this.lookupWorker);
      this.lookupWorker = setTimeout(worker, this.delay);
    },

    process: function (items) {
      var that = this;

      items = $.grep(items, function (item) {
        return that.matcher(item);
      });

      items = this.sorter(items);

      if (!items.length && !this.options.addItem) {
        return this.shown ? this.hide() : this;
      }

      if (items.length > 0) {
        this.$element.data('active', items[0]);
      } else {
        this.$element.data('active', null);
      }

      // Add item
      if (this.options.addItem){
        items.push(this.options.addItem);
      }

      if (this.options.items == 'all') {
        return this.render(items).show();
      } else {
        return this.render(items.slice(0, this.options.items)).show();
      }
    },

    matcher: function (item) {
      var it = this.displayText(item);
      return ~it.toLowerCase().indexOf(this.query.toLowerCase());
    },

    sorter: function (items) {
      var beginswith = [];
      var caseSensitive = [];
      var caseInsensitive = [];
      var item;

      while ((item = items.shift())) {
        var it = this.displayText(item);
        if (!it.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item);
        else if (~it.indexOf(this.query)) caseSensitive.push(item);
        else caseInsensitive.push(item);
      }

      return beginswith.concat(caseSensitive, caseInsensitive);
    },

    highlighter: function (item) {
      var html = $('<div></div>');
      var query = this.query;
      var i = item.toLowerCase().indexOf(query.toLowerCase());
      var len = query.length;
      var leftPart;
      var middlePart;
      var rightPart;
      var strong;
      if (len === 0) {
        return html.text(item).html();
      }
      while (i > -1) {
        leftPart = item.substr(0, i);
        middlePart = item.substr(i, len);
        rightPart = item.substr(i + len);
        strong = $('<strong></strong>').text(middlePart);
        html
          .append(document.createTextNode(leftPart))
          .append(strong);
        item = rightPart;
        i = item.toLowerCase().indexOf(query.toLowerCase());
      }
      return html.append(document.createTextNode(item)).html();
    },

    render: function (items) {
      var that = this;
      var self = this;
      var activeFound = false;
      var data = [];
      var _category = that.options.separator;

      $.each(items, function (key,value) {
        // inject separator
        if (key > 0 && value[_category] !== items[key - 1][_category]){
          data.push({
            __type: 'divider'
          });
        }

        // inject category header
        if (value[_category] && (key === 0 || value[_category] !== items[key - 1][_category])){
          data.push({
            __type: 'category',
            name: value[_category]
          });
        }
        data.push(value);
      });

      items = $(data).map(function (i, item) {
        if ((item.__type || false) == 'category'){
          return $(that.options.headerHtml).text(item.name)[0];
        }

        if ((item.__type || false) == 'divider'){
          return $(that.options.headerDivider)[0];
        }

        var text = self.displayText(item);
        i = $(that.options.item).data('value', item);
        i.find('a').html(that.highlighter(text, item));
        if (text == self.$element.val()) {
          i.addClass('active');
          self.$element.data('active', item);
          activeFound = true;
        }
        return i[0];
      });

      if (this.autoSelect && !activeFound) {
        items.filter(':not(.dropdown-header)').first().addClass('active');
        this.$element.data('active', items.first().data('value'));
      }
      this.$menu.html(items);
      return this;
    },

    displayText: function (item) {
      return typeof item !== 'undefined' && typeof item.name != 'undefined' ? item.name : item;
    },

    next: function (event) {
      var active = this.$menu.find('.active').removeClass('active');
      var next = active.next();

      if (!next.length) {
        next = $(this.$menu.find('li')[0]);
      }

      next.addClass('active');
    },

    prev: function (event) {
      var active = this.$menu.find('.active').removeClass('active');
      var prev = active.prev();

      if (!prev.length) {
        prev = this.$menu.find('li').last();
      }

      prev.addClass('active');
    },

    listen: function () {
      this.$element
        .on('focus',    $.proxy(this.focus, this))
        .on('blur',     $.proxy(this.blur, this))
        .on('keypress', $.proxy(this.keypress, this))
        .on('input',    $.proxy(this.input, this))
        .on('keyup',    $.proxy(this.keyup, this));

      if (this.eventSupported('keydown')) {
        this.$element.on('keydown', $.proxy(this.keydown, this));
      }

      this.$menu
        .on('click', $.proxy(this.click, this))
        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
        .on('mouseleave', 'li', $.proxy(this.mouseleave, this))
        .on('mousedown', $.proxy(this.mousedown,this));
    },

    destroy : function () {
      this.$element.data('typeahead',null);
      this.$element.data('active',null);
      this.$element
        .off('focus')
        .off('blur')
        .off('keypress')
        .off('input')
        .off('keyup');

      if (this.eventSupported('keydown')) {
        this.$element.off('keydown');
      }

      this.$menu.remove();
      this.destroyed = true;
    },

    eventSupported: function (eventName) {
      var isSupported = eventName in this.$element;
      if (!isSupported) {
        this.$element.setAttribute(eventName, 'return;');
        isSupported = typeof this.$element[eventName] === 'function';
      }
      return isSupported;
    },

    move: function (e) {
      if (!this.shown) return;

      switch (e.keyCode) {
        case 9: // tab
        case 13: // enter
        case 27: // escape
          e.preventDefault();
          break;

        case 38: // up arrow
          // with the shiftKey (this is actually the left parenthesis)
          if (e.shiftKey) return;
          e.preventDefault();
          this.prev();
          break;

        case 40: // down arrow
          // with the shiftKey (this is actually the right parenthesis)
          if (e.shiftKey) return;
          e.preventDefault();
          this.next();
          break;
      }
    },

    keydown: function (e) {
      this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27]);
      if (!this.shown && e.keyCode == 40) {
        this.lookup();
      } else {
        this.move(e);
      }
    },

    keypress: function (e) {
      if (this.suppressKeyPressRepeat) return;
      this.move(e);
    },

    input: function (e) {
      // This is a fixed for IE10/11 that fires the input event when a placehoder is changed
      // (https://connect.microsoft.com/IE/feedback/details/810538/ie-11-fires-input-event-on-focus)
      var currentValue = this.$element.val() || this.$element.text();
      if (this.value !== currentValue) {
        this.value = currentValue;
        this.lookup();
      }
    },

    keyup: function (e) {
      if (this.destroyed) {
        return;
      }
      switch (e.keyCode) {
        case 40: // down arrow
        case 38: // up arrow
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
          break;

        case 9: // tab
        case 13: // enter
          if (!this.shown) return;
          this.select();
          break;

        case 27: // escape
          if (!this.shown) return;
          this.hide();
          break;
      }


    },

    focus: function (e) {
      if (!this.focused) {
        this.focused = true;
        if (this.options.showHintOnFocus && this.skipShowHintOnFocus !== true) {
          if(this.options.showHintOnFocus === "all") {
            this.lookup(""); 
          } else {
            this.lookup();
          }
        }
      }
      if (this.skipShowHintOnFocus) {
        this.skipShowHintOnFocus = false;
      }
    },

    blur: function (e) {
      if (!this.mousedover && !this.mouseddown && this.shown) {
        this.hide();
        this.focused = false;
      } else if (this.mouseddown) {
        // This is for IE that blurs the input when user clicks on scroll.
        // We set the focus back on the input and prevent the lookup to occur again
        this.skipShowHintOnFocus = true;
        this.$element.focus();
        this.mouseddown = false;
      } 
    },

    click: function (e) {
      e.preventDefault();
      this.skipShowHintOnFocus = true;
      this.select();
      this.$element.focus();
      this.hide();
    },

    mouseenter: function (e) {
      this.mousedover = true;
      this.$menu.find('.active').removeClass('active');
      $(e.currentTarget).addClass('active');
    },

    mouseleave: function (e) {
      this.mousedover = false;
      if (!this.focused && this.shown) this.hide();
    },

   /**
     * We track the mousedown for IE. When clicking on the menu scrollbar, IE makes the input blur thus hiding the menu.
     */
    mousedown: function (e) {
      this.mouseddown = true;
      this.$menu.one("mouseup", function(e){
        // IE won't fire this, but FF and Chrome will so we reset our flag for them here
        this.mouseddown = false;
      }.bind(this));
    },

  };


  /* TYPEAHEAD PLUGIN DEFINITION
   * =========================== */

  var old = $.fn.typeahead;

  $.fn.typeahead = function (option) {
    var arg = arguments;
    if (typeof option == 'string' && option == 'getActive') {
      return this.data('active');
    }
    return this.each(function () {
      var $this = $(this);
      var data = $this.data('typeahead');
      var options = typeof option == 'object' && option;
      if (!data) $this.data('typeahead', (data = new Typeahead(this, options)));
      if (typeof option == 'string' && data[option]) {
        if (arg.length > 1) {
          data[option].apply(data, Array.prototype.slice.call(arg, 1));
        } else {
          data[option]();
        }
      }
    });
  };

  Typeahead.defaults = {
    source: [],
    items: 8,
    menu: '<ul class="typeahead dropdown-menu" role="listbox"></ul>',
    item: '<li><a class="dropdown-item" href="#" role="option"></a></li>',
    minLength: 1,
    scrollHeight: 0,
    autoSelect: true,
    afterSelect: $.noop,
    addItem: false,
    delay: 0,
    separator: 'category',
    headerHtml: '<li class="dropdown-header"></li>',
    headerDivider: '<li class="divider" role="separator"></li>'
  };

  $.fn.typeahead.Constructor = Typeahead;

 /* TYPEAHEAD NO CONFLICT
  * =================== */

  $.fn.typeahead.noConflict = function () {
    $.fn.typeahead = old;
    return this;
  };


 /* TYPEAHEAD DATA-API
  * ================== */

  $(document).on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
    var $this = $(this);
    if ($this.data('typeahead')) return;
    $this.typeahead($this.data());
  });

}));

/*!
 * bootstrap-tags 1.1.5
 * https://github.com/maxwells/bootstrap-tags
 * Copyright 2013 Max Lahey; Licensed MIT
 */

(function($) {
    (function() {
        window.Tags || (window.Tags = {});
        jQuery(function() {
            $.tags = function(element, options) {
                var key, tag, tagData, value, _i, _len, _ref, _this = this;
                if (options == null) {
                    options = {};
                }
                for (key in options) {
                    value = options[key];
                    this[key] = value;
                }
                this.bootstrapVersion || (this.bootstrapVersion = "3");
                this.readOnly || (this.readOnly = false);
                this.suggestOnClick || (this.suggestOnClick = false);
                this.suggestions || (this.suggestions = []);
                this.restrictTo = options.restrictTo != null ? options.restrictTo.concat(this.suggestions) : false;
                this.exclude || (this.exclude = false);
                this.displayPopovers = options.popovers != null ? true : options.popoverData != null;
                this.popoverTrigger || (this.popoverTrigger = "hover");
                this.tagClass || (this.tagClass = "btn-primary");
                this.tagSize || (this.tagSize = "md");
                this.promptText || (this.promptText = "Enter tags...");
                this.caseInsensitive || (this.caseInsensitive = false);
                this.readOnlyEmptyMessage || (this.readOnlyEmptyMessage = "No tags to display...");
                this.maxNumTags || (this.maxNumTags = -1);
                this.beforeAddingTag || (this.beforeAddingTag = function(tag) {});
                this.afterAddingTag || (this.afterAddingTag = function(tag) {});
                this.beforeDeletingTag || (this.beforeDeletingTag = function(tag) {});
                this.afterDeletingTag || (this.afterDeletingTag = function(tag) {});
                this.definePopover || (this.definePopover = function(tag) {
                    return 'associated content for "' + tag + '"';
                });
                this.excludes || (this.excludes = function() {
                    return false;
                });
                this.tagRemoved || (this.tagRemoved = function(tag) {});
                this.pressedReturn || (this.pressedReturn = function(e) {});
                this.pressedDelete || (this.pressedDelete = function(e) {});
                this.pressedDown || (this.pressedDown = function(e) {});
                this.pressedUp || (this.pressedUp = function(e) {});
                this.$element = $(element);
                if (options.tagData != null) {
                    this.tagsArray = options.tagData;
                } else {
                    tagData = $(".tag-data", this.$element).html();
                    this.tagsArray = tagData != null ? tagData.split(",") : [];
                }
                if (options.popoverData) {
                    this.popoverArray = options.popoverData;
                } else {
                    this.popoverArray = [];
                    _ref = this.tagsArray;
                    for (_i = 0, _len = _ref.length; _i < _len; _i++) {
                        tag = _ref[_i];
                        this.popoverArray.push(null);
                    }
                }
                this.getTags = function() {
                    return _this.tagsArray;
                };
                this.getTagsContent = function() {
                    return _this.popoverArray;
                };
                this.getTagsWithContent = function() {
                    var combined, i, _j, _ref1;
                    combined = [];
                    for (i = _j = 0, _ref1 = _this.tagsArray.length - 1; 0 <= _ref1 ? _j <= _ref1 : _j >= _ref1; i = 0 <= _ref1 ? ++_j : --_j) {
                        combined.push({
                            tag: _this.tagsArray[i],
                            content: _this.popoverArray[i]
                        });
                    }
                    return combined;
                };
                this.getTag = function(tag) {
                    var index;
                    index = _this.tagsArray.indexOf(tag);
                    if (index > -1) {
                        return _this.tagsArray[index];
                    } else {
                        return null;
                    }
                };
                this.getTagWithContent = function(tag) {
                    var index;
                    index = _this.tagsArray.indexOf(tag);
                    return {
                        tag: _this.tagsArray[index],
                        content: _this.popoverArray[index]
                    };
                };
                this.hasTag = function(tag) {
                    return _this.tagsArray.indexOf(tag) > -1;
                };
                this.removeTagClicked = function(e) {
                    if (e.currentTarget.tagName === "A") {
                        _this.removeTag($("span", e.currentTarget.parentElement).html());
                        $(e.currentTarget.parentNode).remove();
                    }
                    return _this;
                };
                this.removeLastTag = function() {
                    if (_this.tagsArray.length > 0) {
                        _this.removeTag(_this.tagsArray[_this.tagsArray.length - 1]);
                        if (_this.canAddByMaxNum()) {
                            _this.enableInput();
                        }
                    }
                    return _this;
                };
                this.removeTag = function(tag) {
                    if (_this.tagsArray.indexOf(tag) > -1) {
                        if (_this.beforeDeletingTag(tag) === false) {
                            return;
                        }
                        _this.popoverArray.splice(_this.tagsArray.indexOf(tag), 1);
                        _this.tagsArray.splice(_this.tagsArray.indexOf(tag), 1);
                        _this.renderTags();
                        _this.afterDeletingTag(tag);
                        if (_this.canAddByMaxNum()) {
                            _this.enableInput();
                        }
                    }
                    return _this;
                };
                this.canAddByRestriction = function(tag) {
                    return this.restrictTo === false || this.restrictTo.indexOf(tag) !== -1;
                };
                this.canAddByExclusion = function(tag) {
                    return (this.exclude === false || this.exclude.indexOf(tag) === -1) && !this.excludes(tag);
                };
                this.canAddByMaxNum = function() {
                    return this.maxNumTags === -1 || this.tagsArray.length < this.maxNumTags;
                };
                this.addTag = function(tag) {
                    var associatedContent;
                    if (_this.canAddByRestriction(tag) && !_this.hasTag(tag) && tag.length > 0 && _this.canAddByExclusion(tag) && _this.canAddByMaxNum()) {
                        if (_this.beforeAddingTag(tag) === false) {
                            return;
                        }
                        associatedContent = _this.definePopover(tag);
                        _this.popoverArray.push(associatedContent || null);
                        _this.tagsArray.push(tag);
                        _this.afterAddingTag(tag);
                        _this.renderTags();
                        if (!_this.canAddByMaxNum()) {
                            _this.disableInput();
                        }
                    }
                    return _this;
                };
                this.addTagWithContent = function(tag, content) {
                    if (_this.canAddByRestriction(tag) && !_this.hasTag(tag) && tag.length > 0) {
                        if (_this.beforeAddingTag(tag) === false) {
                            return;
                        }
                        _this.tagsArray.push(tag);
                        _this.popoverArray.push(content);
                        _this.afterAddingTag(tag);
                        _this.renderTags();
                    }
                    return _this;
                };
                this.renameTag = function(name, newName) {
                    _this.tagsArray[_this.tagsArray.indexOf(name)] = newName;
                    _this.renderTags();
                    return _this;
                };
                this.setPopover = function(tag, popoverContent) {
                    _this.popoverArray[_this.tagsArray.indexOf(tag)] = popoverContent;
                    _this.renderTags();
                    return _this;
                };
                this.clickHandler = function(e) {
                    return _this.makeSuggestions(e, true);
                };
                this.keyDownHandler = function(e) {
                    var k, numSuggestions;
                    k = e.keyCode != null ? e.keyCode : e.which;
                    switch (k) {
                      case 13:
                        e.preventDefault();
                        _this.pressedReturn(e);
                        tag = e.target.value;
                        if (_this.suggestedIndex !== -1) {
                            tag = _this.suggestionList[_this.suggestedIndex];
                        }
                        _this.addTag(tag);
                        e.target.value = "";
                        _this.renderTags();
                        return _this.hideSuggestions();

                      case 46:
                      case 8:
                        _this.pressedDelete(e);
                        if (e.target.value === "") {
                            _this.removeLastTag();
                        }
                        if (e.target.value.length === 1) {
                            return _this.hideSuggestions();
                        }
                        break;

                      case 40:
                        _this.pressedDown(e);
                        if (_this.input.val() === "" && (_this.suggestedIndex === -1 || _this.suggestedIndex == null)) {
                            _this.makeSuggestions(e, true);
                        }
                        numSuggestions = _this.suggestionList.length;
                        _this.suggestedIndex = _this.suggestedIndex < numSuggestions - 1 ? _this.suggestedIndex + 1 : numSuggestions - 1;
                        _this.selectSuggested(_this.suggestedIndex);
                        if (_this.suggestedIndex >= 0) {
                            return _this.scrollSuggested(_this.suggestedIndex);
                        }
                        break;

                      case 38:
                        _this.pressedUp(e);
                        _this.suggestedIndex = _this.suggestedIndex > 0 ? _this.suggestedIndex - 1 : 0;
                        _this.selectSuggested(_this.suggestedIndex);
                        if (_this.suggestedIndex >= 0) {
                            return _this.scrollSuggested(_this.suggestedIndex);
                        }
                        break;

                      case 9:
                      case 27:
                        _this.hideSuggestions();
                        return _this.suggestedIndex = -1;
                    }
                };
                this.keyUpHandler = function(e) {
                    var k;
                    k = e.keyCode != null ? e.keyCode : e.which;
                    if (k !== 40 && k !== 38 && k !== 27) {
                        return _this.makeSuggestions(e, false);
                    }
                };
                this.getSuggestions = function(str, overrideLengthCheck) {
                    var _this = this;
                    this.suggestionList = [];
                    if (this.caseInsensitive) {
                        str = str.toLowerCase();
                    }
                    $.each(this.suggestions, function(i, suggestion) {
                        var suggestionVal;
                        suggestionVal = _this.caseInsensitive ? suggestion.substring(0, str.length).toLowerCase() : suggestion.substring(0, str.length);
                        if (_this.tagsArray.indexOf(suggestion) < 0 && suggestionVal === str && (str.length > 0 || overrideLengthCheck)) {
                            return _this.suggestionList.push(suggestion);
                        }
                    });
                    return this.suggestionList;
                };
                this.makeSuggestions = function(e, overrideLengthCheck, val) {
                    if (val == null) {
                        val = e.target.value != null ? e.target.value : e.target.textContent;
                    }
                    _this.suggestedIndex = -1;
                    _this.$suggestionList.html("");
                    $.each(_this.getSuggestions(val, overrideLengthCheck), function(i, suggestion) {
                        return _this.$suggestionList.append(_this.template("tags_suggestion", {
                            suggestion: suggestion
                        }));
                    });
                    _this.$(".tags-suggestion").mouseover(_this.selectSuggestedMouseOver);
                    _this.$(".tags-suggestion").click(_this.suggestedClicked);
                    if (_this.suggestionList.length > 0) {
                        return _this.showSuggestions();
                    } else {
                        return _this.hideSuggestions();
                    }
                };
                this.suggestedClicked = function(e) {
                    tag = e.target.textContent;
                    if (_this.suggestedIndex !== -1) {
                        tag = _this.suggestionList[_this.suggestedIndex];
                    }
                    _this.addTag(tag);
                    _this.input.val("");
                    _this.makeSuggestions(e, false, "");
                    _this.input.focus();
                    return _this.hideSuggestions();
                };
                this.hideSuggestions = function() {
                    return _this.$(".tags-suggestion-list").css({
                        display: "none"
                    });
                };
                this.showSuggestions = function() {
                    return _this.$(".tags-suggestion-list").css({
                        display: "block"
                    });
                };
                this.selectSuggestedMouseOver = function(e) {
                    $(".tags-suggestion").removeClass("tags-suggestion-highlighted");
                    $(e.target).addClass("tags-suggestion-highlighted");
                    $(e.target).mouseout(_this.selectSuggestedMousedOut);
                    return _this.suggestedIndex = _this.$(".tags-suggestion").index($(e.target));
                };
                this.selectSuggestedMousedOut = function(e) {
                    return $(e.target).removeClass("tags-suggestion-highlighted");
                };
                this.selectSuggested = function(i) {
                    var tagElement;
                    $(".tags-suggestion").removeClass("tags-suggestion-highlighted");
                    tagElement = _this.$(".tags-suggestion").eq(i);
                    return tagElement.addClass("tags-suggestion-highlighted");
                };
                this.scrollSuggested = function(i) {
                    var pos, tagElement, topElement, topPos;
                    tagElement = _this.$(".tags-suggestion").eq(i);
                    topElement = _this.$(".tags-suggestion").eq(0);
                    pos = tagElement.position();
                    topPos = topElement.position();
                    if (pos != null) {
                        return _this.$(".tags-suggestion-list").scrollTop(pos.top - topPos.top);
                    }
                };
                this.adjustInputPosition = function() {
                    var pBottom, pLeft, pTop, pWidth, tagElement, tagPosition;
                    tagElement = _this.$(".tag").last();
                    tagPosition = tagElement.position();
                    pLeft = tagPosition != null ? tagPosition.left + tagElement.outerWidth(true) : 0;
                    pTop = tagPosition != null ? tagPosition.top : 0;
                    pWidth = _this.$element.width() - pLeft;
                    $(".tags-input", _this.$element).css({
                        paddingLeft: Math.max(pLeft, 0),
                        paddingTop: Math.max(pTop, 0),
                        width: pWidth
                    });
                    pBottom = tagPosition != null ? tagPosition.top + tagElement.outerHeight(true) : 22;
                    return _this.$element.css({
                        paddingBottom: pBottom - _this.$element.height()
                    });
                };
                this.renderTags = function() {
                    var tagList;
                    tagList = _this.$(".tags");
                    tagList.html("");
                    _this.input.attr("placeholder", _this.tagsArray.length === 0 ? _this.promptText : "");
                    $.each(_this.tagsArray, function(i, tag) {
                        tag = $(_this.formatTag(i, tag));
                        $("a", tag).click(_this.removeTagClicked);
                        $("a", tag).mouseover(_this.toggleCloseColor);
                        $("a", tag).mouseout(_this.toggleCloseColor);
                        if (_this.displayPopovers) {
                            _this.initializePopoverFor(tag, _this.tagsArray[i], _this.popoverArray[i]);
                        }
                        return tagList.append(tag);
                    });
                    return _this.adjustInputPosition();
                };
                this.renderReadOnly = function() {
                    var tagList;
                    tagList = _this.$(".tags");
                    tagList.html(_this.tagsArray.length === 0 ? _this.readOnlyEmptyMessage : "");
                    return $.each(_this.tagsArray, function(i, tag) {
                        tag = $(_this.formatTag(i, tag, true));
                        if (_this.displayPopovers) {
                            _this.initializePopoverFor(tag, _this.tagsArray[i], _this.popoverArray[i]);
                        }
                        return tagList.append(tag);
                    });
                };
                this.disableInput = function() {
                    return this.$("input").prop("disabled", true);
                };
                this.enableInput = function() {
                    return this.$("input").prop("disabled", false);
                };
                this.initializePopoverFor = function(tag, title, content) {
                    options = {
                        title: title,
                        content: content,
                        placement: "bottom"
                    };
                    if (_this.popoverTrigger === "hoverShowClickHide") {
                        $(tag).mouseover(function() {
                            $(tag).popover("show");
                            return $(".tag").not(tag).popover("hide");
                        });
                        $(document).click(function() {
                            return $(tag).popover("hide");
                        });
                    } else {
                        options.trigger = _this.popoverTrigger;
                    }
                    return $(tag).popover(options);
                };
                this.toggleCloseColor = function(e) {
                    var opacity, tagAnchor;
                    tagAnchor = $(e.currentTarget);
                    opacity = tagAnchor.css("opacity");
                    opacity = opacity < .8 ? 1 : .6;
                    return tagAnchor.css({
                        opacity: opacity
                    });
                };
                this.formatTag = function(i, tag, isReadOnly) {
                    var escapedTag;
                    if (isReadOnly == null) {
                        isReadOnly = false;
                    }
                    escapedTag = tag.replace("<", "&lt;").replace(">", "&gt;");
                    return _this.template("tag", {
                        tag: escapedTag,
                        tagClass: _this.tagClass,
                        isPopover: _this.displayPopovers,
                        isReadOnly: isReadOnly,
                        tagSize: _this.tagSize
                    });
                };
                this.addDocumentListeners = function() {
                    return $(document).mouseup(function(e) {
                        var container;
                        container = _this.$(".tags-suggestion-list");
                        if (container.has(e.target).length === 0) {
                            return _this.hideSuggestions();
                        }
                    });
                };
                this.template = function(name, options) {
                    return Tags.Templates.Template(this.getBootstrapVersion(), name, options);
                };
                this.$ = function(selector) {
                    return $(selector, this.$element);
                };
                this.getBootstrapVersion = function() {
                    return Tags.bootstrapVersion || this.bootstrapVersion;
                };
                this.initializeDom = function() {
                    return this.$element.append(this.template("tags_container"));
                };
                this.init = function() {
                    this.$element.addClass("bootstrap-tags").addClass("bootstrap-" + this.getBootstrapVersion());
                    this.initializeDom();
                    if (this.readOnly) {
                        this.renderReadOnly();
                        this.removeTag = function() {};
                        this.removeTagClicked = function() {};
                        this.removeLastTag = function() {};
                        this.addTag = function() {};
                        this.addTagWithContent = function() {};
                        this.renameTag = function() {};
                        return this.setPopover = function() {};
                    } else {
                        this.input = $(this.template("input", {
                            tagSize: this.tagSize
                        }));
                        if (this.suggestOnClick) {
                            this.input.click(this.clickHandler);
                        }
                        this.input.keydown(this.keyDownHandler);
                        this.input.keyup(this.keyUpHandler);
                        this.$element.append(this.input);
                        this.$suggestionList = $(this.template("suggestion_list"));
                        this.$element.append(this.$suggestionList);
                        this.renderTags();
                        if (!this.canAddByMaxNum()) {
                            this.disableInput();
                        }
                        return this.addDocumentListeners();
                    }
                };
                this.init();
                return this;
            };
            return $.fn.tags = function(options) {
                var stopOn, tagsObject;
                tagsObject = {};
                stopOn = typeof options === "number" ? options : -1;
                this.each(function(i, el) {
                    var $el;
                    $el = $(el);
                    if ($el.data("tags") == null) {
                        $el.data("tags", new $.tags(this, options));
                    }
                    if (stopOn === i || i === 0) {
                        return tagsObject = $el.data("tags");
                    }
                });
                return tagsObject;
            };
        });
    }).call(this);
    (function() {
        window.Tags || (window.Tags = {});
        Tags.Helpers || (Tags.Helpers = {});
        Tags.Helpers.addPadding = function(string, amount, doPadding) {
            if (amount == null) {
                amount = 1;
            }
            if (doPadding == null) {
                doPadding = true;
            }
            if (!doPadding) {
                return string;
            }
            if (amount === 0) {
                return string;
            }
            return Tags.Helpers.addPadding("&nbsp" + string + "&nbsp", amount - 1);
        };
    }).call(this);
    (function() {
        var _base;
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        (_base = Tags.Templates)["2"] || (_base["2"] = {});
        Tags.Templates["2"].input = function(options) {
            var tagSize;
            if (options == null) {
                options = {};
            }
            tagSize = function() {
                switch (options.tagSize) {
                  case "sm":
                    return "small";

                  case "md":
                    return "medium";

                  case "lg":
                    return "large";
                }
            }();
            return "<input type='text' class='tags-input input-" + tagSize + "' />";
        };
    }).call(this);
    (function() {
        var _base;
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        (_base = Tags.Templates)["2"] || (_base["2"] = {});
        Tags.Templates["2"].tag = function(options) {
            if (options == null) {
                options = {};
            }
            return "<div class='tag label " + options.tagClass + " " + options.tagSize + "' " + (options.isPopover ? "rel='popover'" : "") + ">    <span>" + Tags.Helpers.addPadding(options.tag, 2, options.isReadOnly) + "</span>    " + (options.isReadOnly ? "" : "<a><i class='remove icon-remove-sign icon-white' /></a>") + "  </div>";
        };
    }).call(this);
    (function() {
        var _base;
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        (_base = Tags.Templates)["3"] || (_base["3"] = {});
        Tags.Templates["3"].input = function(options) {
            if (options == null) {
                options = {};
            }
            return "<input type='text' class='form-control tags-input input-" + options.tagSize + "' />";
        };
    }).call(this);
    (function() {
        var _base;
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        (_base = Tags.Templates)["3"] || (_base["3"] = {});
        Tags.Templates["3"].tag = function(options) {
            if (options == null) {
                options = {};
            }
            return "<div class='tag label " + options.tagClass + " " + options.tagSize + "' " + (options.isPopover ? "rel='popover'" : "") + ">    <span>" + Tags.Helpers.addPadding(options.tag, 2, options.isReadOnly) + "</span>    " + (options.isReadOnly ? "" : "<a><i class='remove fa fa-remove glyphicon-white' /></a>") + "  </div>";
        };
    }).call(this);
    (function() {
        var _base;
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        (_base = Tags.Templates).shared || (_base.shared = {});
        Tags.Templates.shared.suggestion_list = function(options) {
            if (options == null) {
                options = {};
            }
            return '<ul class="tags-suggestion-list dropdown-menu"></ul>';
        };
    }).call(this);
    (function() {
        var _base;
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        (_base = Tags.Templates).shared || (_base.shared = {});
        Tags.Templates.shared.tags_container = function(options) {
            if (options == null) {
                options = {};
            }
            return '<div class="tags"></div>';
        };
    }).call(this);
    (function() {
        var _base;
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        (_base = Tags.Templates).shared || (_base.shared = {});
        Tags.Templates.shared.tags_suggestion = function(options) {
            if (options == null) {
                options = {};
            }
            return "<li class='tags-suggestion'>" + options.suggestion + "</li>";
        };
    }).call(this);
    (function() {
        window.Tags || (window.Tags = {});
        Tags.Templates || (Tags.Templates = {});
        Tags.Templates.Template = function(version, templateName, options) {
            if (Tags.Templates[version] != null) {
                if (Tags.Templates[version][templateName] != null) {
                    return Tags.Templates[version][templateName](options);
                }
            }
            return Tags.Templates.shared[templateName](options);
        };
    }).call(this);
})(window.jQuery);
/* =========================================================
 * bootstrap-treeview.js v1.2.0
 * =========================================================
 * Copyright 2013 Jonathan Miles
 * Project URL : http://www.jondmiles.com/bootstrap-treeview
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

;(function ($, window, document, undefined) {

	/*global jQuery, console*/

	'use strict';

	var pluginName = 'treeview';

	var _default = {};

	_default.settings = {

		injectStyle: true,

		levels: 2,

		expandIcon: 'glyphicon glyphicon-plus',
		collapseIcon: 'glyphicon glyphicon-minus',
		emptyIcon: 'glyphicon',
		nodeIcon: '',
		selectedIcon: '',
		checkedIcon: 'glyphicon glyphicon-check',
		uncheckedIcon: 'glyphicon glyphicon-unchecked',

		color: undefined, // '#000000',
		backColor: undefined, // '#FFFFFF',
		borderColor: undefined, // '#dddddd',
		onhoverColor: '#F5F5F5',
		selectedColor: '#FFFFFF',
		selectedBackColor: '#428bca',
		searchResultColor: '#D9534F',
		searchResultBackColor: undefined, //'#FFFFFF',

		enableLinks: false,
		highlightSelected: true,
		highlightSearchResults: true,
		showBorder: true,
		showIcon: true,
		showCheckbox: false,
		showTags: false,
		multiSelect: false,

		// Event handlers
		onNodeChecked: undefined,
		onNodeCollapsed: undefined,
		onNodeDisabled: undefined,
		onNodeEnabled: undefined,
		onNodeExpanded: undefined,
		onNodeSelected: undefined,
		onNodeUnchecked: undefined,
		onNodeUnselected: undefined,
		onSearchComplete: undefined,
		onSearchCleared: undefined
	};

	_default.options = {
		silent: false,
		ignoreChildren: false
	};

	_default.searchOptions = {
		ignoreCase: true,
		exactMatch: false,
		revealResults: true
	};

	var Tree = function (element, options) {

		this.$element = $(element);
		this.elementId = element.id;
		this.styleId = this.elementId + '-style';

		this.init(options);

		return {

			// Options (public access)
			options: this.options,

			// Initialize / destroy methods
			init: $.proxy(this.init, this),
			remove: $.proxy(this.remove, this),

			// Get methods
			getNode: $.proxy(this.getNode, this),
			getParent: $.proxy(this.getParent, this),
			getSiblings: $.proxy(this.getSiblings, this),
			getSelected: $.proxy(this.getSelected, this),
			getUnselected: $.proxy(this.getUnselected, this),
			getExpanded: $.proxy(this.getExpanded, this),
			getCollapsed: $.proxy(this.getCollapsed, this),
			getChecked: $.proxy(this.getChecked, this),
			getUnchecked: $.proxy(this.getUnchecked, this),
			getDisabled: $.proxy(this.getDisabled, this),
			getEnabled: $.proxy(this.getEnabled, this),

			// Select methods
			selectNode: $.proxy(this.selectNode, this),
			unselectNode: $.proxy(this.unselectNode, this),
			toggleNodeSelected: $.proxy(this.toggleNodeSelected, this),

			// Expand / collapse methods
			collapseAll: $.proxy(this.collapseAll, this),
			collapseNode: $.proxy(this.collapseNode, this),
			expandAll: $.proxy(this.expandAll, this),
			expandNode: $.proxy(this.expandNode, this),
			toggleNodeExpanded: $.proxy(this.toggleNodeExpanded, this),
			revealNode: $.proxy(this.revealNode, this),

			// Expand / collapse methods
			checkAll: $.proxy(this.checkAll, this),
			checkNode: $.proxy(this.checkNode, this),
			uncheckAll: $.proxy(this.uncheckAll, this),
			uncheckNode: $.proxy(this.uncheckNode, this),
			toggleNodeChecked: $.proxy(this.toggleNodeChecked, this),

			// Disable / enable methods
			disableAll: $.proxy(this.disableAll, this),
			disableNode: $.proxy(this.disableNode, this),
			enableAll: $.proxy(this.enableAll, this),
			enableNode: $.proxy(this.enableNode, this),
			toggleNodeDisabled: $.proxy(this.toggleNodeDisabled, this),

			// Search methods
			search: $.proxy(this.search, this),
			clearSearch: $.proxy(this.clearSearch, this)
		};
	};

	Tree.prototype.init = function (options) {

		this.tree = [];
		this.nodes = [];

		if (options.data) {
			if (typeof options.data === 'string') {
				options.data = $.parseJSON(options.data);
			}
			this.tree = $.extend(true, [], options.data);
			delete options.data;
		}
		this.options = $.extend({}, _default.settings, options);

		this.destroy();
		this.subscribeEvents();
		this.setInitialStates({ nodes: this.tree }, 0);
		this.render();
	};

	Tree.prototype.remove = function () {
		this.destroy();
		$.removeData(this, pluginName);
		$('#' + this.styleId).remove();
	};

	Tree.prototype.destroy = function () {

		if (!this.initialized) return;

		this.$wrapper.remove();
		this.$wrapper = null;

		// Switch off events
		this.unsubscribeEvents();

		// Reset this.initialized flag
		this.initialized = false;
	};

	Tree.prototype.unsubscribeEvents = function () {

		this.$element.off('click');
		this.$element.off('nodeChecked');
		this.$element.off('nodeCollapsed');
		this.$element.off('nodeDisabled');
		this.$element.off('nodeEnabled');
		this.$element.off('nodeExpanded');
		this.$element.off('nodeSelected');
		this.$element.off('nodeUnchecked');
		this.$element.off('nodeUnselected');
		this.$element.off('searchComplete');
		this.$element.off('searchCleared');
	};

	Tree.prototype.subscribeEvents = function () {

		this.unsubscribeEvents();

		this.$element.on('click', $.proxy(this.clickHandler, this));

		if (typeof (this.options.onNodeChecked) === 'function') {
			this.$element.on('nodeChecked', this.options.onNodeChecked);
		}

		if (typeof (this.options.onNodeCollapsed) === 'function') {
			this.$element.on('nodeCollapsed', this.options.onNodeCollapsed);
		}

		if (typeof (this.options.onNodeDisabled) === 'function') {
			this.$element.on('nodeDisabled', this.options.onNodeDisabled);
		}

		if (typeof (this.options.onNodeEnabled) === 'function') {
			this.$element.on('nodeEnabled', this.options.onNodeEnabled);
		}

		if (typeof (this.options.onNodeExpanded) === 'function') {
			this.$element.on('nodeExpanded', this.options.onNodeExpanded);
		}

		if (typeof (this.options.onNodeSelected) === 'function') {
			this.$element.on('nodeSelected', this.options.onNodeSelected);
		}

		if (typeof (this.options.onNodeUnchecked) === 'function') {
			this.$element.on('nodeUnchecked', this.options.onNodeUnchecked);
		}

		if (typeof (this.options.onNodeUnselected) === 'function') {
			this.$element.on('nodeUnselected', this.options.onNodeUnselected);
		}

		if (typeof (this.options.onSearchComplete) === 'function') {
			this.$element.on('searchComplete', this.options.onSearchComplete);
		}

		if (typeof (this.options.onSearchCleared) === 'function') {
			this.$element.on('searchCleared', this.options.onSearchCleared);
		}
	};

	/*
		Recurse the tree structure and ensure all nodes have
		valid initial states.  User defined states will be preserved.
		For performance we also take this opportunity to
		index nodes in a flattened structure
	*/
	Tree.prototype.setInitialStates = function (node, level) {

		if (!node.nodes) return;
		level += 1;

		var parent = node;
		var _this = this;
		$.each(node.nodes, function checkStates(index, node) {

			// nodeId : unique, incremental identifier
			node.nodeId = _this.nodes.length;

			// parentId : transversing up the tree
			node.parentId = parent.nodeId;

			// if not provided set selectable default value
			if (!node.hasOwnProperty('selectable')) {
				node.selectable = true;
			}
 
			// where provided we should preserve states
			node.state = node.state || {};

			// set checked state; unless set always false
			if (!node.state.hasOwnProperty('checked')) {
				node.state.checked = false;
			}

			// set enabled state; unless set always false
			if (!node.state.hasOwnProperty('disabled')) {
				node.state.disabled = false;
			}

			// set expanded state; if not provided based on levels
			if (!node.state.hasOwnProperty('expanded')) {
				if (!node.state.disabled &&
						(level < _this.options.levels) &&
						(node.nodes && node.nodes.length > 0)) {
					node.state.expanded = true;
				}
				else {
					node.state.expanded = false;
				}
			}

			// set selected state; unless set always false
			if (!node.state.hasOwnProperty('selected')) {
				node.state.selected = false;
			}

			// index nodes in a flattened structure for use later
			_this.nodes.push(node);

			// recurse child nodes and transverse the tree
			if (node.nodes) {
				_this.setInitialStates(node, level);
			}
		});
	};

	Tree.prototype.clickHandler = function (event) {

		if (!this.options.enableLinks) event.preventDefault();

		var target = $(event.target);
		var node = this.findNode(target);
		if (!node || node.state.disabled) return;
		
		var classList = target.attr('class') ? target.attr('class').split(' ') : [];
		if ((classList.indexOf('expand-icon') !== -1)) {

			this.toggleExpandedState(node, _default.options);
			this.render();
		}
		else if ((classList.indexOf('check-icon') !== -1)) {
			
			this.toggleCheckedState(node, _default.options);
			this.render();
		}
		else {
			
			if (node.selectable) {
				this.toggleSelectedState(node, _default.options);
			} else {
				this.toggleExpandedState(node, _default.options);
			}

			this.render();
		}
	};

	// Looks up the DOM for the closest parent list item to retrieve the
	// data attribute nodeid, which is used to lookup the node in the flattened structure.
	Tree.prototype.findNode = function (target) {

		var nodeId = target.closest('li.list-group-item').attr('data-nodeid');
		var node = this.nodes[nodeId];

		if (!node) {
			console.log('Error: node does not exist');
		}
		return node;
	};

	Tree.prototype.toggleExpandedState = function (node, options) {
		if (!node) return;
		this.setExpandedState(node, !node.state.expanded, options);
	};

	Tree.prototype.setExpandedState = function (node, state, options) {

		if (state === node.state.expanded) return;

		if (state && node.nodes) {

			// Expand a node
			node.state.expanded = true;
			if (!options.silent) {
				this.$element.trigger('nodeExpanded', $.extend(true, {}, node));
			}
		}
		else if (!state) {

			// Collapse a node
			node.state.expanded = false;
			if (!options.silent) {
				this.$element.trigger('nodeCollapsed', $.extend(true, {}, node));
			}

			// Collapse child nodes
			if (node.nodes && !options.ignoreChildren) {
				$.each(node.nodes, $.proxy(function (index, node) {
					this.setExpandedState(node, false, options);
				}, this));
			}
		}
	};

	Tree.prototype.toggleSelectedState = function (node, options) {
		if (!node) return;
		this.setSelectedState(node, !node.state.selected, options);
	};

	Tree.prototype.setSelectedState = function (node, state, options) {

		if (state === node.state.selected) return;

		if (state) {

			// If multiSelect false, unselect previously selected
			if (!this.options.multiSelect) {
				$.each(this.findNodes('true', 'g', 'state.selected'), $.proxy(function (index, node) {
					this.setSelectedState(node, false, options);
				}, this));
			}

			// Continue selecting node
			node.state.selected = true;
			if (!options.silent) {
				this.$element.trigger('nodeSelected', $.extend(true, {}, node));
			}
		}
		else {

			// Unselect node
			node.state.selected = false;
			if (!options.silent) {
				this.$element.trigger('nodeUnselected', $.extend(true, {}, node));
			}
		}
	};

	Tree.prototype.toggleCheckedState = function (node, options) {
		if (!node) return;
		this.setCheckedState(node, !node.state.checked, options);
	};

	Tree.prototype.setCheckedState = function (node, state, options) {

		if (state === node.state.checked) return;

		if (state) {

			// Check node
			node.state.checked = true;

			if (!options.silent) {
				this.$element.trigger('nodeChecked', $.extend(true, {}, node));
			}
		}
		else {

			// Uncheck node
			node.state.checked = false;
			if (!options.silent) {
				this.$element.trigger('nodeUnchecked', $.extend(true, {}, node));
			}
		}
	};

	Tree.prototype.setDisabledState = function (node, state, options) {

		if (state === node.state.disabled) return;

		if (state) {

			// Disable node
			node.state.disabled = true;

			// Disable all other states
			this.setExpandedState(node, false, options);
			this.setSelectedState(node, false, options);
			this.setCheckedState(node, false, options);

			if (!options.silent) {
				this.$element.trigger('nodeDisabled', $.extend(true, {}, node));
			}
		}
		else {

			// Enabled node
			node.state.disabled = false;
			if (!options.silent) {
				this.$element.trigger('nodeEnabled', $.extend(true, {}, node));
			}
		}
	};

	Tree.prototype.render = function () {

		if (!this.initialized) {

			// Setup first time only components
			this.$element.addClass(pluginName);
			this.$wrapper = $(this.template.list);

			this.injectStyle();

			this.initialized = true;
		}

		this.$element.empty().append(this.$wrapper.empty());

		// Build tree
		this.buildTree(this.tree, 0);
	};

	// Starting from the root node, and recursing down the
	// structure we build the tree one node at a time
	Tree.prototype.buildTree = function (nodes, level) {

		if (!nodes) return;
		level += 1;

		var _this = this;
		$.each(nodes, function addNodes(id, node) {

			var treeItem = $(_this.template.item)
				.addClass('node-' + _this.elementId)
				.addClass(node.state.checked ? 'node-checked' : '')
				.addClass(node.state.disabled ? 'node-disabled': '')
				.addClass(node.state.selected ? 'node-selected' : '')
				.addClass(node.searchResult ? 'search-result' : '') 
				.attr('data-nodeid', node.nodeId)
				.attr('style', _this.buildStyleOverride(node));

			// Add indent/spacer to mimic tree structure
			for (var i = 0; i < (level - 1); i++) {
				treeItem.append(_this.template.indent);
			}

			// Add expand, collapse or empty spacer icons
			var classList = [];
			if (node.nodes) {
				classList.push('expand-icon');
				if (node.state.expanded) {
					classList.push(_this.options.collapseIcon);
				}
				else {
					classList.push(_this.options.expandIcon);
				}
			}
			else {
				classList.push(_this.options.emptyIcon);
			}

			treeItem
				.append($(_this.template.icon)
					.addClass(classList.join(' '))
				);


			// Add node icon
			if (_this.options.showIcon) {
				
				var classList = ['node-icon'];

				classList.push(node.icon || _this.options.nodeIcon);
				if (node.state.selected) {
					classList.pop();
					classList.push(node.selectedIcon || _this.options.selectedIcon || 
									node.icon || _this.options.nodeIcon);
				}

				treeItem
					.append($(_this.template.icon)
						.addClass(classList.join(' '))
					);
			}

			// Add check / unchecked icon
			if (_this.options.showCheckbox) {

				var classList = ['check-icon'];
				if (node.state.checked) {
					classList.push(_this.options.checkedIcon); 
				}
				else {
					classList.push(_this.options.uncheckedIcon);
				}

				treeItem
					.append($(_this.template.icon)
						.addClass(classList.join(' '))
					);
			}

			// Add text
			if (_this.options.enableLinks) {
				// Add hyperlink
				treeItem
					.append($(_this.template.link)
						.attr('href', node.href)
						.append(node.text)
					);
			}
			else {
				// otherwise just text
				treeItem
					.append(node.text);
			}

			// Add tags as badges
			if (_this.options.showTags && node.tags) {
				$.each(node.tags, function addTag(id, tag) {
					treeItem
						.append($(_this.template.badge)
							.append(tag)
						);
				});
			}

			// Add item to the tree
			_this.$wrapper.append(treeItem);

			// Recursively add child ndoes
			if (node.nodes && node.state.expanded && !node.state.disabled) {
				return _this.buildTree(node.nodes, level);
			}
		});
	};

	// Define any node level style override for
	// 1. selectedNode
	// 2. node|data assigned color overrides
	Tree.prototype.buildStyleOverride = function (node) {

		if (node.state.disabled) return '';

		var color = node.color;
		var backColor = node.backColor;

		if (this.options.highlightSelected && node.state.selected) {
			if (this.options.selectedColor) {
				color = this.options.selectedColor;
			}
			if (this.options.selectedBackColor) {
				backColor = this.options.selectedBackColor;
			}
		}

		if (this.options.highlightSearchResults && node.searchResult && !node.state.disabled) {
			if (this.options.searchResultColor) {
				color = this.options.searchResultColor;
			}
			if (this.options.searchResultBackColor) {
				backColor = this.options.searchResultBackColor;
			}
		}

		return 'color:' + color +
			';background-color:' + backColor + ';';
	};

	// Add inline style into head
	Tree.prototype.injectStyle = function () {

		if (this.options.injectStyle && !document.getElementById(this.styleId)) {
			$('<style type="text/css" id="' + this.styleId + '"> ' + this.buildStyle() + ' </style>').appendTo('head');
		}
	};

	// Construct trees style based on user options
	Tree.prototype.buildStyle = function () {

		var style = '.node-' + this.elementId + '{';

		if (this.options.color) {
			style += 'color:' + this.options.color + ';';
		}

		if (this.options.backColor) {
			style += 'background-color:' + this.options.backColor + ';';
		}

		if (!this.options.showBorder) {
			style += 'border:none;';
		}
		else if (this.options.borderColor) {
			style += 'border:1px solid ' + this.options.borderColor + ';';
		}
		style += '}';

		if (this.options.onhoverColor) {
			style += '.node-' + this.elementId + ':not(.node-disabled):hover{' +
				'background-color:' + this.options.onhoverColor + ';' +
			'}';
		}

		return this.css + style;
	};

	Tree.prototype.template = {
		list: '<ul class="list-group"></ul>',
		item: '<li class="list-group-item"></li>',
		indent: '<span class="indent"></span>',
		icon: '<span class="icon"></span>',
		link: '<a href="#" style="color:inherit;"></a>',
		badge: '<span class="badge"></span>'
	};

	Tree.prototype.css = '.treeview .list-group-item{cursor:pointer}.treeview span.indent{margin-left:10px;margin-right:10px}.treeview span.icon{width:12px;margin-right:5px}.treeview .node-disabled{color:silver;cursor:not-allowed}'


	/**
		Returns a single node object that matches the given node id.
		@param {Number} nodeId - A node's unique identifier
		@return {Object} node - Matching node
	*/
	Tree.prototype.getNode = function (nodeId) {
		return this.nodes[nodeId];
	};

	/**
		Returns the parent node of a given node, if valid otherwise returns undefined.
		@param {Object|Number} identifier - A valid node or node id
		@returns {Object} node - The parent node
	*/
	Tree.prototype.getParent = function (identifier) {
		var node = this.identifyNode(identifier);
		return this.nodes[node.parentId];
	};

	/**
		Returns an array of sibling nodes for a given node, if valid otherwise returns undefined.
		@param {Object|Number} identifier - A valid node or node id
		@returns {Array} nodes - Sibling nodes
	*/
	Tree.prototype.getSiblings = function (identifier) {
		var node = this.identifyNode(identifier);
		var parent = this.getParent(node);
		var nodes = parent ? parent.nodes : this.tree;
		return nodes.filter(function (obj) {
				return obj.nodeId !== node.nodeId;
			});
	};

	/**
		Returns an array of selected nodes.
		@returns {Array} nodes - Selected nodes
	*/
	Tree.prototype.getSelected = function () {
		return this.findNodes('true', 'g', 'state.selected');
	};

	/**
		Returns an array of unselected nodes.
		@returns {Array} nodes - Unselected nodes
	*/
	Tree.prototype.getUnselected = function () {
		return this.findNodes('false', 'g', 'state.selected');
	};

	/**
		Returns an array of expanded nodes.
		@returns {Array} nodes - Expanded nodes
	*/
	Tree.prototype.getExpanded = function () {
		return this.findNodes('true', 'g', 'state.expanded');
	};

	/**
		Returns an array of collapsed nodes.
		@returns {Array} nodes - Collapsed nodes
	*/
	Tree.prototype.getCollapsed = function () {
		return this.findNodes('false', 'g', 'state.expanded');
	};

	/**
		Returns an array of checked nodes.
		@returns {Array} nodes - Checked nodes
	*/
	Tree.prototype.getChecked = function () {
		return this.findNodes('true', 'g', 'state.checked');
	};

	/**
		Returns an array of unchecked nodes.
		@returns {Array} nodes - Unchecked nodes
	*/
	Tree.prototype.getUnchecked = function () {
		return this.findNodes('false', 'g', 'state.checked');
	};

	/**
		Returns an array of disabled nodes.
		@returns {Array} nodes - Disabled nodes
	*/
	Tree.prototype.getDisabled = function () {
		return this.findNodes('true', 'g', 'state.disabled');
	};

	/**
		Returns an array of enabled nodes.
		@returns {Array} nodes - Enabled nodes
	*/
	Tree.prototype.getEnabled = function () {
		return this.findNodes('false', 'g', 'state.disabled');
	};


	/**
		Set a node state to selected
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.selectNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setSelectedState(node, true, options);
		}, this));

		this.render();
	};

	/**
		Set a node state to unselected
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.unselectNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setSelectedState(node, false, options);
		}, this));

		this.render();
	};

	/**
		Toggles a node selected state; selecting if unselected, unselecting if selected.
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.toggleNodeSelected = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.toggleSelectedState(node, options);
		}, this));

		this.render();
	};


	/**
		Collapse all tree nodes
		@param {optional Object} options
	*/
	Tree.prototype.collapseAll = function (options) {
		var identifiers = this.findNodes('true', 'g', 'state.expanded');
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setExpandedState(node, false, options);
		}, this));

		this.render();
	};

	/**
		Collapse a given tree node
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.collapseNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setExpandedState(node, false, options);
		}, this));

		this.render();
	};

	/**
		Expand all tree nodes
		@param {optional Object} options
	*/
	Tree.prototype.expandAll = function (options) {
		options = $.extend({}, _default.options, options);

		if (options && options.levels) {
			this.expandLevels(this.tree, options.levels, options);
		}
		else {
			var identifiers = this.findNodes('false', 'g', 'state.expanded');
			this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
				this.setExpandedState(node, true, options);
			}, this));
		}

		this.render();
	};

	/**
		Expand a given tree node
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.expandNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setExpandedState(node, true, options);
			if (node.nodes && (options && options.levels)) {
				this.expandLevels(node.nodes, options.levels-1, options);
			}
		}, this));

		this.render();
	};

	Tree.prototype.expandLevels = function (nodes, level, options) {
		options = $.extend({}, _default.options, options);

		$.each(nodes, $.proxy(function (index, node) {
			this.setExpandedState(node, (level > 0) ? true : false, options);
			if (node.nodes) {
				this.expandLevels(node.nodes, level-1, options);
			}
		}, this));
	};

	/**
		Reveals a given tree node, expanding the tree from node to root.
		@param {Object|Number|Array} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.revealNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			var parentNode = this.getParent(node);
			while (parentNode) {
				this.setExpandedState(parentNode, true, options);
				parentNode = this.getParent(parentNode);
			};
		}, this));

		this.render();
	};

	/**
		Toggles a nodes expanded state; collapsing if expanded, expanding if collapsed.
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.toggleNodeExpanded = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.toggleExpandedState(node, options);
		}, this));
		
		this.render();
	};


	/**
		Check all tree nodes
		@param {optional Object} options
	*/
	Tree.prototype.checkAll = function (options) {
		var identifiers = this.findNodes('false', 'g', 'state.checked');
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setCheckedState(node, true, options);
		}, this));

		this.render();
	};

	/**
		Check a given tree node
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.checkNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setCheckedState(node, true, options);
		}, this));

		this.render();
	};

	/**
		Uncheck all tree nodes
		@param {optional Object} options
	*/
	Tree.prototype.uncheckAll = function (options) {
		var identifiers = this.findNodes('true', 'g', 'state.checked');
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setCheckedState(node, false, options);
		}, this));

		this.render();
	};

	/**
		Uncheck a given tree node
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.uncheckNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setCheckedState(node, false, options);
		}, this));

		this.render();
	};

	/**
		Toggles a nodes checked state; checking if unchecked, unchecking if checked.
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.toggleNodeChecked = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.toggleCheckedState(node, options);
		}, this));

		this.render();
	};


	/**
		Disable all tree nodes
		@param {optional Object} options
	*/
	Tree.prototype.disableAll = function (options) {
		var identifiers = this.findNodes('false', 'g', 'state.disabled');
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setDisabledState(node, true, options);
		}, this));

		this.render();
	};

	/**
		Disable a given tree node
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.disableNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setDisabledState(node, true, options);
		}, this));

		this.render();
	};

	/**
		Enable all tree nodes
		@param {optional Object} options
	*/
	Tree.prototype.enableAll = function (options) {
		var identifiers = this.findNodes('true', 'g', 'state.disabled');
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setDisabledState(node, false, options);
		}, this));

		this.render();
	};

	/**
		Enable a given tree node
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.enableNode = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setDisabledState(node, false, options);
		}, this));

		this.render();
	};

	/**
		Toggles a nodes disabled state; disabling is enabled, enabling if disabled.
		@param {Object|Number} identifiers - A valid node, node id or array of node identifiers
		@param {optional Object} options
	*/
	Tree.prototype.toggleNodeDisabled = function (identifiers, options) {
		this.forEachIdentifier(identifiers, options, $.proxy(function (node, options) {
			this.setDisabledState(node, !node.state.disabled, options);
		}, this));

		this.render();
	};


	/**
		Common code for processing multiple identifiers
	*/
	Tree.prototype.forEachIdentifier = function (identifiers, options, callback) {

		options = $.extend({}, _default.options, options);

		if (!(identifiers instanceof Array)) {
			identifiers = [identifiers];
		}

		$.each(identifiers, $.proxy(function (index, identifier) {
			callback(this.identifyNode(identifier), options);
		}, this));	
	};

	/*
		Identifies a node from either a node id or object
	*/
	Tree.prototype.identifyNode = function (identifier) {
		return ((typeof identifier) === 'number') ?
						this.nodes[identifier] :
						identifier;
	};

	/**
		Searches the tree for nodes (text) that match given criteria
		@param {String} pattern - A given string to match against
		@param {optional Object} options - Search criteria options
		@return {Array} nodes - Matching nodes
	*/
	Tree.prototype.search = function (pattern, options) {
		options = $.extend({}, _default.searchOptions, options);

		this.clearSearch({ render: false });

		var results = [];
		if (pattern && pattern.length > 0) {

			if (options.exactMatch) {
				pattern = '^' + pattern + '$';
			}

			var modifier = 'g';
			if (options.ignoreCase) {
				modifier += 'i';
			}

			results = this.findNodes(pattern, modifier);

			// Add searchResult property to all matching nodes
			// This will be used to apply custom styles
			// and when identifying result to be cleared
			$.each(results, function (index, node) {
				node.searchResult = true;
			})
		}

		// If revealResults, then render is triggered from revealNode
		// otherwise we just call render.
		if (options.revealResults) {
			this.revealNode(results);
		}
		else {
			this.render();
		}

		this.$element.trigger('searchComplete', $.extend(true, {}, results));

		return results;
	};

	/**
		Clears previous search results
	*/
	Tree.prototype.clearSearch = function (options) {

		options = $.extend({}, { render: true }, options);

		var results = $.each(this.findNodes('true', 'g', 'searchResult'), function (index, node) {
			node.searchResult = false;
		});

		if (options.render) {
			this.render();	
		}
		
		this.$element.trigger('searchCleared', $.extend(true, {}, results));
	};

	/**
		Find nodes that match a given criteria
		@param {String} pattern - A given string to match against
		@param {optional String} modifier - Valid RegEx modifiers
		@param {optional String} attribute - Attribute to compare pattern against
		@return {Array} nodes - Nodes that match your criteria
	*/
	Tree.prototype.findNodes = function (pattern, modifier, attribute) {

		modifier = modifier || 'g';
		attribute = attribute || 'text';

		var _this = this;
		return $.grep(this.nodes, function (node) {
			var val = _this.getNodeValue(node, attribute);
			if (typeof val === 'string') {
				return val.match(new RegExp(pattern, modifier));
			}
		});
	};

	/**
		Recursive find for retrieving nested attributes values
		All values are return as strings, unless invalid
		@param {Object} obj - Typically a node, could be any object
		@param {String} attr - Identifies an object property using dot notation
		@return {String} value - Matching attributes string representation
	*/
	Tree.prototype.getNodeValue = function (obj, attr) {
		var index = attr.indexOf('.');
		if (index > 0) {
			var _obj = obj[attr.substring(0, index)];
			var _attr = attr.substring(index + 1, attr.length);
			return this.getNodeValue(_obj, _attr);
		}
		else {
			if (obj.hasOwnProperty(attr)) {
				return obj[attr].toString();
			}
			else {
				return undefined;
			}
		}
	};

	var logError = function (message) {
		if (window.console) {
			window.console.error(message);
		}
	};

	// Prevent against multiple instantiations,
	// handle updates and method calls
	$.fn[pluginName] = function (options, args) {

		var result;

		this.each(function () {
			var _this = $.data(this, pluginName);
			if (typeof options === 'string') {
				if (!_this) {
					logError('Not initialized, can not call method : ' + options);
				}
				else if (!$.isFunction(_this[options]) || options.charAt(0) === '_') {
					logError('No such method : ' + options);
				}
				else {
					if (!(args instanceof Array)) {
						args = [ args ];
					}
					result = _this[options].apply(_this, args);
				}
			}
			else if (typeof options === 'boolean') {
				result = _this;
			}
			else {
				$.data(this, pluginName, new Tree(this, $.extend(true, {}, options)));
			}
		});

		return result || this;
	};

})(jQuery, window, document);


/*Zippy  framework*/

 
 
 
function getUpdate(q)
{
 
    
fetch(q)
  .then((response)=>{
    return response.text();
  })
  .then((data) => {
   eval(data);
  });
    
}
 

function submitForm(formid, q)
{
 
    
    var f = document.getElementById(formid)  ;
    let formdata = new FormData(f);
    
    
    
fetch(q,{
  method: 'POST',
        body: formdata  
})
  .then((response) => {
    return response.text();
  })
  .then((data) => {
   eval(data);
  });    
    
} 
 
function beforeZippy(id) {

    var i = id.lastIndexOf('_');
    if (i > 0) {
        id = id.substring(0, i);
    }

    var f = 'check_' + id;

    if (typeof window[f] == 'function') {


        return window[f]();
    }
    return true;
}
