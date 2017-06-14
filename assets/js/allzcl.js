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

/*!
 * Datepicker for Bootstrap v1.7.0-dev (https://github.com/eternicode/bootstrap-datepicker)
 *
 * Copyright 2012 Stefan Petre
 * Improvements by Andrew Rowls
 * Licensed under the Apache License v2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */

(function (factory) {
    if (typeof define === "function" && define.amd) {
        define(["jquery"], factory);
    } else if (typeof exports === 'object') {
        factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(function ($, undefined) {

    function UTCDate() {
        return new Date(Date.UTC.apply(Date, arguments));
    }
    function UTCToday() {
        var today = new Date();
        return UTCDate(today.getFullYear(), today.getMonth(), today.getDate());
    }
    function isUTCEquals(date1, date2) {
        return (
                date1.getUTCFullYear() === date2.getUTCFullYear() &&
                date1.getUTCMonth() === date2.getUTCMonth() &&
                date1.getUTCDate() === date2.getUTCDate()
                );
    }
    function alias(method) {
        return function () {
            return this[method].apply(this, arguments);
        };
    }
    function isValidDate(d) {
        return d && !isNaN(d.getTime());
    }

    var DateArray = (function () {
        var extras = {
            get: function (i) {
                return this.slice(i)[0];
            },
            contains: function (d) {
                // Array.indexOf is not cross-browser;
                // $.inArray doesn't work with Dates
                var val = d && d.valueOf();
                for (var i = 0, l = this.length; i < l; i++)
                    // Use date arithmetic to allow dates with different times to match
                    if (0 <= this[i].valueOf() - val && this[i].valueOf() - val < 1000 * 60 * 60 * 24)
                        return i;
                return -1;
            },
            remove: function (i) {
                this.splice(i, 1);
            },
            replace: function (new_array) {
                if (!new_array)
                    return;
                if (!$.isArray(new_array))
                    new_array = [new_array];
                this.clear();
                this.push.apply(this, new_array);
            },
            clear: function () {
                this.length = 0;
            },
            copy: function () {
                var a = new DateArray();
                a.replace(this);
                return a;
            }
        };

        return function () {
            var a = [];
            a.push.apply(a, arguments);
            $.extend(a, extras);
            return a;
        };
    })();


    // Picker object

    var Datepicker = function (element, options) {
        $.data(element, 'datepicker', this);
        this._process_options(options);

        this.dates = new DateArray();
        this.viewDate = this.o.defaultViewDate;
        this.focusDate = null;

        this.element = $(element);
        this.isInput = this.element.is('input');
        this.inputField = this.isInput ? this.element : this.element.find('input');
        this.component = this.element.hasClass('date') ? this.element.find('.add-on, .input-group-addon, .btn') : false;
        if (this.component && this.component.length === 0)
            this.component = false;
        this.isInline = !this.component && this.element.is('div');

        this.picker = $(DPGlobal.template);

        // Checking templates and inserting
        if (this._check_template(this.o.templates.leftArrow)) {
            this.picker.find('.prev').html(this.o.templates.leftArrow);
        }

        if (this._check_template(this.o.templates.rightArrow)) {
            this.picker.find('.next').html(this.o.templates.rightArrow);
        }

        this._buildEvents();
        this._attachEvents();

        if (this.isInline) {
            this.picker.addClass('datepicker-inline').appendTo(this.element);
        }
        else {
            this.picker.addClass('datepicker-dropdown dropdown-menu');
        }

        if (this.o.rtl) {
            this.picker.addClass('datepicker-rtl');
        }

        if (this.o.calendarWeeks) {
            this.picker.find('.datepicker-days .datepicker-switch, thead .datepicker-title, tfoot .today, tfoot .clear')
                    .attr('colspan', function (i, val) {
                        return Number(val) + 1;
                    });
        }

        this._allow_update = false;

        this.setStartDate(this._o.startDate);
        this.setEndDate(this._o.endDate);
        this.setDaysOfWeekDisabled(this.o.daysOfWeekDisabled);
        this.setDaysOfWeekHighlighted(this.o.daysOfWeekHighlighted);
        this.setDatesDisabled(this.o.datesDisabled);

        this.setViewMode(this.o.startView);
        this.fillDow();
        this.fillMonths();

        this._allow_update = true;

        this.update();

        if (this.isInline) {
            this.show();
        }
    };

    Datepicker.prototype = {
        constructor: Datepicker,
        _resolveViewName: function (view) {
            $.each(DPGlobal.viewModes, function (i, viewMode) {
                if (view === i || $.inArray(view, viewMode.names) !== -1) {
                    view = i;
                    return false;
                }
            });

            return view;
        },
        _resolveDaysOfWeek: function (daysOfWeek) {
            if (!$.isArray(daysOfWeek))
                daysOfWeek = daysOfWeek.split(/[,\s]*/);
            return $.map(daysOfWeek, Number);
        },
        _check_template: function (tmp) {
            try {
                // If empty
                if (tmp === undefined || tmp === "") {
                    return false;
                }
                // If no html, everything ok
                if ((tmp.match(/[<>]/g) || []).length <= 0) {
                    return true;
                }
                // Checking if html is fine
                var jDom = $(tmp);
                return jDom.length > 0;
            }
            catch (ex) {
                return false;
            }
        },
        _process_options: function (opts) {
            // Store raw options for reference
            this._o = $.extend({}, this._o, opts);
            // Processed options
            var o = this.o = $.extend({}, this._o);

            // Check if "de-DE" style date is available, if not language should
            // fallback to 2 letter code eg "de"
            var lang = o.language;
            if (!dates[lang]) {
                lang = lang.split('-')[0];
                if (!dates[lang])
                    lang = defaults.language;
            }
            o.language = lang;

            // Retrieve view index from any aliases
            o.startView = this._resolveViewName(o.startView);
            o.minViewMode = this._resolveViewName(o.minViewMode);
            o.maxViewMode = this._resolveViewName(o.maxViewMode);

            // Check view is between min and max
            o.startView = Math.max(this.o.minViewMode, Math.min(this.o.maxViewMode, o.startView));

            // true, false, or Number > 0
            if (o.multidate !== true) {
                o.multidate = Number(o.multidate) || false;
                if (o.multidate !== false)
                    o.multidate = Math.max(0, o.multidate);
            }
            o.multidateSeparator = String(o.multidateSeparator);

            o.weekStart %= 7;
            o.weekEnd = (o.weekStart + 6) % 7;

            var format = DPGlobal.parseFormat(o.format);
            if (o.startDate !== -Infinity) {
                if (!!o.startDate) {
                    if (o.startDate instanceof Date)
                        o.startDate = this._local_to_utc(this._zero_time(o.startDate));
                    else
                        o.startDate = DPGlobal.parseDate(o.startDate, format, o.language, o.assumeNearbyYear);
                }
                else {
                    o.startDate = -Infinity;
                }
            }
            if (o.endDate !== Infinity) {
                if (!!o.endDate) {
                    if (o.endDate instanceof Date)
                        o.endDate = this._local_to_utc(this._zero_time(o.endDate));
                    else
                        o.endDate = DPGlobal.parseDate(o.endDate, format, o.language, o.assumeNearbyYear);
                }
                else {
                    o.endDate = Infinity;
                }
            }

            o.daysOfWeekDisabled = this._resolveDaysOfWeek(o.daysOfWeekDisabled || []);
            o.daysOfWeekHighlighted = this._resolveDaysOfWeek(o.daysOfWeekHighlighted || []);

            o.datesDisabled = o.datesDisabled || [];
            if (!$.isArray(o.datesDisabled)) {
                o.datesDisabled = o.datesDisabled.split(',');
            }
            o.datesDisabled = $.map(o.datesDisabled, function (d) {
                return DPGlobal.parseDate(d, format, o.language, o.assumeNearbyYear);
            });

            var plc = String(o.orientation).toLowerCase().split(/\s+/g),
                    _plc = o.orientation.toLowerCase();
            plc = $.grep(plc, function (word) {
                return /^auto|left|right|top|bottom$/.test(word);
            });
            o.orientation = {x: 'auto', y: 'auto'};
            if (!_plc || _plc === 'auto')
                ; // no action
            else if (plc.length === 1) {
                switch (plc[0]) {
                    case 'top':
                    case 'bottom':
                        o.orientation.y = plc[0];
                        break;
                    case 'left':
                    case 'right':
                        o.orientation.x = plc[0];
                        break;
                }
            }
            else {
                _plc = $.grep(plc, function (word) {
                    return /^left|right$/.test(word);
                });
                o.orientation.x = _plc[0] || 'auto';

                _plc = $.grep(plc, function (word) {
                    return /^top|bottom$/.test(word);
                });
                o.orientation.y = _plc[0] || 'auto';
            }
            if (o.defaultViewDate) {
                var year = o.defaultViewDate.year || new Date().getFullYear();
                var month = o.defaultViewDate.month || 0;
                var day = o.defaultViewDate.day || 1;
                o.defaultViewDate = UTCDate(year, month, day);
            } else {
                o.defaultViewDate = UTCToday();
            }
        },
        _events: [],
        _secondaryEvents: [],
        _applyEvents: function (evs) {
            for (var i = 0, el, ch, ev; i < evs.length; i++) {
                el = evs[i][0];
                if (evs[i].length === 2) {
                    ch = undefined;
                    ev = evs[i][1];
                } else if (evs[i].length === 3) {
                    ch = evs[i][1];
                    ev = evs[i][2];
                }
                el.on(ev, ch);
            }
        },
        _unapplyEvents: function (evs) {
            for (var i = 0, el, ev, ch; i < evs.length; i++) {
                el = evs[i][0];
                if (evs[i].length === 2) {
                    ch = undefined;
                    ev = evs[i][1];
                } else if (evs[i].length === 3) {
                    ch = evs[i][1];
                    ev = evs[i][2];
                }
                el.off(ev, ch);
            }
        },
        _buildEvents: function () {
            var events = {
                keyup: $.proxy(function (e) {
                    if ($.inArray(e.keyCode, [27, 37, 39, 38, 40, 32, 13, 9]) === -1)
                        this.update();
                }, this),
                keydown: $.proxy(this.keydown, this),
                paste: $.proxy(this.paste, this)
            };

            if (this.o.showOnFocus === true) {
                events.focus = $.proxy(this.show, this);
            }

            if (this.isInput) { // single input
                this._events = [
                    [this.element, events]
                ];
            }
            // component: input + button
            else if (this.component && this.inputField.length) {
                this._events = [
                    // For components that are not readonly, allow keyboard nav
                    [this.inputField, events],
                    [this.component, {
                            click: $.proxy(this.show, this)
                        }]
                ];
            }
            else {
                this._events = [
                    [this.element, {
                            click: $.proxy(this.show, this),
                            keydown: $.proxy(this.keydown, this)
                        }]
                ];
            }
            this._events.push(
                    // Component: listen for blur on element descendants
                            [this.element, '*', {
                                    blur: $.proxy(function (e) {
                                        this._focused_from = e.target;
                                    }, this)
                                }],
                            // Input: listen for blur on element
                                    [this.element, {
                                            blur: $.proxy(function (e) {
                                                this._focused_from = e.target;
                                            }, this)
                                        }]
                                    );

                            if (this.o.immediateUpdates) {
                                // Trigger input updates immediately on changed year/month
                                this._events.push([this.element, {
                                        'changeYear changeMonth': $.proxy(function (e) {
                                            this.update(e.date);
                                        }, this)
                                    }]);
                            }

                            this._secondaryEvents = [
                                [this.picker, {
                                        click: $.proxy(this.click, this)
                                    }],
                                [this.picker, '.prev, .next', {
                                        click: $.proxy(this.navArrowsClick, this)
                                    }],
                                [$(window), {
                                        resize: $.proxy(this.place, this)
                                    }],
                                [$(document), {
                                        'mousedown touchstart': $.proxy(function (e) {
                                            // Clicked outside the datepicker, hide it
                                            if (!(
                                                    this.element.is(e.target) ||
                                                    this.element.find(e.target).length ||
                                                    this.picker.is(e.target) ||
                                                    this.picker.find(e.target).length ||
                                                    this.isInline
                                                    )) {
                                                this.hide();
                                            }
                                        }, this)
                                    }]
                            ];
                        },
                        _attachEvents: function () {
                            this._detachEvents();
                            this._applyEvents(this._events);
                        },
                        _detachEvents: function () {
                            this._unapplyEvents(this._events);
                        },
                        _attachSecondaryEvents: function () {
                            this._detachSecondaryEvents();
                            this._applyEvents(this._secondaryEvents);
                        },
                        _detachSecondaryEvents: function () {
                            this._unapplyEvents(this._secondaryEvents);
                        },
                        _trigger: function (event, altdate) {
                            var date = altdate || this.dates.get(-1),
                                    local_date = this._utc_to_local(date);

                            this.element.trigger({
                                type: event,
                                date: local_date,
                                viewMode: this.viewMode,
                                dates: $.map(this.dates, this._utc_to_local),
                                format: $.proxy(function (ix, format) {
                                    if (arguments.length === 0) {
                                        ix = this.dates.length - 1;
                                        format = this.o.format;
                                    } else if (typeof ix === 'string') {
                                        format = ix;
                                        ix = this.dates.length - 1;
                                    }
                                    format = format || this.o.format;
                                    var date = this.dates.get(ix);
                                    return DPGlobal.formatDate(date, format, this.o.language);
                                }, this)
                            });
                        },
                        show: function () {
                            if (this.inputField.prop('disabled') || (this.inputField.prop('readonly') && this.o.enableOnReadonly === false))
                                return;
                            if (!this.isInline)
                                this.picker.appendTo(this.o.container);
                            this.place();
                            this.picker.show();
                            this._attachSecondaryEvents();
                            this._trigger('show');
                            if ((window.navigator.msMaxTouchPoints || 'ontouchstart' in document) && this.o.disableTouchKeyboard) {
                                $(this.element).blur();
                            }
                            return this;
                        },
                        hide: function () {
                            if (this.isInline || !this.picker.is(':visible'))
                                return this;
                            this.focusDate = null;
                            this.picker.hide().detach();
                            this._detachSecondaryEvents();
                            this.setViewMode(this.o.startView);

                            if (this.o.forceParse && this.inputField.val())
                                this.setValue();
                            this._trigger('hide');
                            return this;
                        },
                        destroy: function () {
                            this.hide();
                            this._detachEvents();
                            this._detachSecondaryEvents();
                            this.picker.remove();
                            delete this.element.data().datepicker;
                            if (!this.isInput) {
                                delete this.element.data().date;
                            }
                            return this;
                        },
                        paste: function (e) {
                            var dateString;
                            if (e.originalEvent.clipboardData && e.originalEvent.clipboardData.types
                                    && $.inArray('text/plain', e.originalEvent.clipboardData.types) !== -1) {
                                dateString = e.originalEvent.clipboardData.getData('text/plain');
                            } else if (window.clipboardData) {
                                dateString = window.clipboardData.getData('Text');
                            } else {
                                return;
                            }
                            this.setDate(dateString);
                            this.update();
                            e.preventDefault();
                        },
                        _utc_to_local: function (utc) {
                            return utc && new Date(utc.getTime() + (utc.getTimezoneOffset() * 60000));
                        },
                        _local_to_utc: function (local) {
                            return local && new Date(local.getTime() - (local.getTimezoneOffset() * 60000));
                        },
                        _zero_time: function (local) {
                            return local && new Date(local.getFullYear(), local.getMonth(), local.getDate());
                        },
                        _zero_utc_time: function (utc) {
                            return utc && UTCDate(utc.getUTCFullYear(), utc.getUTCMonth(), utc.getUTCDate());
                        },
                        getDates: function () {
                            return $.map(this.dates, this._utc_to_local);
                        },
                        getUTCDates: function () {
                            return $.map(this.dates, function (d) {
                                return new Date(d);
                            });
                        },
                        getDate: function () {
                            return this._utc_to_local(this.getUTCDate());
                        },
                        getUTCDate: function () {
                            var selected_date = this.dates.get(-1);
                            if (selected_date !== undefined) {
                                return new Date(selected_date);
                            } else {
                                return null;
                            }
                        },
                        clearDates: function () {
                            this.inputField.val('');
                            this.update();
                            this._trigger('changeDate');

                            if (this.o.autoclose) {
                                this.hide();
                            }
                        },
                        setDates: function () {
                            var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
                            this.update.apply(this, args);
                            this._trigger('changeDate');
                            this.setValue();
                            return this;
                        },
                        setUTCDates: function () {
                            var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
                            this.setDates.apply(this, $.map(args, this._utc_to_local));
                            return this;
                        },
                        setDate: alias('setDates'),
                        setUTCDate: alias('setUTCDates'),
                        remove: alias('destroy'),
                        setValue: function () {
                            var formatted = this.getFormattedDate();
                            this.inputField.val(formatted);
                            return this;
                        },
                        getFormattedDate: function (format) {
                            if (format === undefined)
                                format = this.o.format;

                            var lang = this.o.language;
                            return $.map(this.dates, function (d) {
                                return DPGlobal.formatDate(d, format, lang);
                            }).join(this.o.multidateSeparator);
                        },
                        getStartDate: function () {
                            return this.o.startDate;
                        },
                        setStartDate: function (startDate) {
                            this._process_options({startDate: startDate});
                            this.update();
                            this.updateNavArrows();
                            return this;
                        },
                        getEndDate: function () {
                            return this.o.endDate;
                        },
                        setEndDate: function (endDate) {
                            this._process_options({endDate: endDate});
                            this.update();
                            this.updateNavArrows();
                            return this;
                        },
                        setDaysOfWeekDisabled: function (daysOfWeekDisabled) {
                            this._process_options({daysOfWeekDisabled: daysOfWeekDisabled});
                            this.update();
                            return this;
                        },
                        setDaysOfWeekHighlighted: function (daysOfWeekHighlighted) {
                            this._process_options({daysOfWeekHighlighted: daysOfWeekHighlighted});
                            this.update();
                            return this;
                        },
                        setDatesDisabled: function (datesDisabled) {
                            this._process_options({datesDisabled: datesDisabled});
                            this.update();
                            return this;
                        },
                        place: function () {
                            if (this.isInline)
                                return this;
                            var calendarWidth = this.picker.outerWidth(),
                                    calendarHeight = this.picker.outerHeight(),
                                    visualPadding = 10,
                                    container = $(this.o.container),
                                    windowWidth = container.width(),
                                    scrollTop = this.o.container === 'body' ? $(document).scrollTop() : container.scrollTop(),
                                    appendOffset = container.offset();

                            var parentsZindex = [];
                            this.element.parents().each(function () {
                                var itemZIndex = $(this).css('z-index');
                                if (itemZIndex !== 'auto' && itemZIndex !== 0)
                                    parentsZindex.push(parseInt(itemZIndex));
                            });
                            var zIndex = Math.max.apply(Math, parentsZindex) + this.o.zIndexOffset;
                            var offset = this.component ? this.component.parent().offset() : this.element.offset();
                            var height = this.component ? this.component.outerHeight(true) : this.element.outerHeight(false);
                            var width = this.component ? this.component.outerWidth(true) : this.element.outerWidth(false);
                            var left = offset.left - appendOffset.left,
                                    top = offset.top - appendOffset.top;

                            if (this.o.container !== 'body') {
                                top += scrollTop;
                            }

                            this.picker.removeClass(
                                    'datepicker-orient-top datepicker-orient-bottom ' +
                                    'datepicker-orient-right datepicker-orient-left'
                                    );

                            if (this.o.orientation.x !== 'auto') {
                                this.picker.addClass('datepicker-orient-' + this.o.orientation.x);
                                if (this.o.orientation.x === 'right')
                                    left -= calendarWidth - width;
                            }
                            // auto x orientation is best-placement: if it crosses a window
                            // edge, fudge it sideways
                            else {
                                if (offset.left < 0) {
                                    // component is outside the window on the left side. Move it into visible range
                                    this.picker.addClass('datepicker-orient-left');
                                    left -= offset.left - visualPadding;
                                } else if (left + calendarWidth > windowWidth) {
                                    // the calendar passes the widow right edge. Align it to component right side
                                    this.picker.addClass('datepicker-orient-right');
                                    left += width - calendarWidth;
                                } else {
                                    if (this.o.rtl) {
                                        // Default to right
                                        this.picker.addClass('datepicker-orient-right');
                                    } else {
                                        // Default to left
                                        this.picker.addClass('datepicker-orient-left');
                                    }
                                }
                            }

                            // auto y orientation is best-situation: top or bottom, no fudging,
                            // decision based on which shows more of the calendar
                            var yorient = this.o.orientation.y,
                                    top_overflow;
                            if (yorient === 'auto') {
                                top_overflow = -scrollTop + top - calendarHeight;
                                yorient = top_overflow < 0 ? 'bottom' : 'top';
                            }

                            this.picker.addClass('datepicker-orient-' + yorient);
                            if (yorient === 'top')
                                top -= calendarHeight + parseInt(this.picker.css('padding-top'));
                            else
                                top += height;

                            if (this.o.rtl) {
                                var right = windowWidth - (left + width);
                                this.picker.css({
                                    top: top,
                                    right: right,
                                    zIndex: zIndex
                                });
                            } else {
                                this.picker.css({
                                    top: top,
                                    left: left,
                                    zIndex: zIndex
                                });
                            }
                            return this;
                        },
                        _allow_update: true,
                        update: function () {
                            if (!this._allow_update)
                                return this;

                            var oldDates = this.dates.copy(),
                                    dates = [],
                                    fromArgs = false;
                            if (arguments.length) {
                                $.each(arguments, $.proxy(function (i, date) {
                                    if (date instanceof Date)
                                        date = this._local_to_utc(date);
                                    dates.push(date);
                                }, this));
                                fromArgs = true;
                            } else {
                                dates = this.isInput
                                        ? this.element.val()
                                        : this.element.data('date') || this.inputField.val();
                                if (dates && this.o.multidate)
                                    dates = dates.split(this.o.multidateSeparator);
                                else
                                    dates = [dates];
                                delete this.element.data().date;
                            }

                            dates = $.map(dates, $.proxy(function (date) {
                                return DPGlobal.parseDate(date, this.o.format, this.o.language, this.o.assumeNearbyYear);
                            }, this));
                            dates = $.grep(dates, $.proxy(function (date) {
                                return (
                                        !this.dateWithinRange(date) ||
                                        !date
                                        );
                            }, this), true);
                            this.dates.replace(dates);

                            if (this.dates.length)
                                this.viewDate = new Date(this.dates.get(-1));
                            else if (this.viewDate < this.o.startDate)
                                this.viewDate = new Date(this.o.startDate);
                            else if (this.viewDate > this.o.endDate)
                                this.viewDate = new Date(this.o.endDate);
                            else
                                this.viewDate = this.o.defaultViewDate;

                            if (fromArgs) {
                                // setting date by clicking
                                this.setValue();
                                this.element.change();
                            }
                            else if (this.dates.length) {
                                // setting date by typing
                                if (String(oldDates) !== String(this.dates) && fromArgs) {
                                    this._trigger('changeDate');
                                    this.element.change();
                                }
                            }
                            if (!this.dates.length && oldDates.length) {
                                this._trigger('clearDate');
                                this.element.change();
                            }

                            this.fill();
                            return this;
                        },
                        fillDow: function () {
                            var dowCnt = this.o.weekStart,
                                    html = '<tr>';
                            if (this.o.calendarWeeks) {
                                html += '<th class="cw">&#160;</th>';
                            }
                            while (dowCnt < this.o.weekStart + 7) {
                                html += '<th class="dow';
                                if ($.inArray(dowCnt, this.o.daysOfWeekDisabled) !== -1)
                                    html += ' disabled';
                                html += '">' + dates[this.o.language].daysMin[(dowCnt++) % 7] + '</th>';
                            }
                            html += '</tr>';
                            this.picker.find('.datepicker-days thead').append(html);
                        },
                        fillMonths: function () {
                            var localDate = this._utc_to_local(this.viewDate);
                            var html = '',
                                    i = 0;
                            while (i < 12) {
                                var focused = localDate && localDate.getMonth() === i ? ' focused' : '';
                                html += '<span class="month' + focused + '">' + dates[this.o.language].monthsShort[i++] + '</span>';
                            }
                            this.picker.find('.datepicker-months td').html(html);
                        },
                        setRange: function (range) {
                            if (!range || !range.length)
                                delete this.range;
                            else
                                this.range = $.map(range, function (d) {
                                    return d.valueOf();
                                });
                            this.fill();
                        },
                        getClassNames: function (date) {
                            var cls = [],
                                    year = this.viewDate.getUTCFullYear(),
                                    month = this.viewDate.getUTCMonth(),
                                    today = UTCToday();
                            if (date.getUTCFullYear() < year || (date.getUTCFullYear() === year && date.getUTCMonth() < month)) {
                                cls.push('old');
                            } else if (date.getUTCFullYear() > year || (date.getUTCFullYear() === year && date.getUTCMonth() > month)) {
                                cls.push('new');
                            }
                            if (this.focusDate && date.valueOf() === this.focusDate.valueOf())
                                cls.push('focused');
                            // Compare internal UTC date with UTC today, not local today
                            if (this.o.todayHighlight && isUTCEquals(date, today)) {
                                cls.push('today');
                            }
                            if (this.dates.contains(date) !== -1)
                                cls.push('active');
                            if (!this.dateWithinRange(date)) {
                                cls.push('disabled');
                            }
                            if (this.dateIsDisabled(date)) {
                                cls.push('disabled', 'disabled-date');
                            }
                            if ($.inArray(date.getUTCDay(), this.o.daysOfWeekHighlighted) !== -1) {
                                cls.push('highlighted');
                            }

                            if (this.range) {
                                if (date > this.range[0] && date < this.range[this.range.length - 1]) {
                                    cls.push('range');
                                }
                                if ($.inArray(date.valueOf(), this.range) !== -1) {
                                    cls.push('selected');
                                }
                                if (date.valueOf() === this.range[0]) {
                                    cls.push('range-start');
                                }
                                if (date.valueOf() === this.range[this.range.length - 1]) {
                                    cls.push('range-end');
                                }
                            }
                            return cls;
                        },
                        _fill_yearsView: function (selector, cssClass, factor, step, currentYear, startYear, endYear, callback) {
                            var html, view, year, steps, startStep, endStep, thisYear, i, classes, tooltip, before;

                            html = '';
                            view = this.picker.find(selector);
                            year = parseInt(currentYear / factor, 10) * factor;
                            startStep = parseInt(startYear / step, 10) * step;
                            endStep = parseInt(endYear / step, 10) * step;
                            steps = $.map(this.dates, function (d) {
                                return parseInt(d.getUTCFullYear() / step, 10) * step;
                            });

                            view.find('.datepicker-switch').text(year + '-' + (year + step * 9));

                            thisYear = year - step;
                            for (i = -1; i < 11; i += 1) {
                                classes = [cssClass];
                                tooltip = null;

                                if (i === -1) {
                                    classes.push('old');
                                } else if (i === 10) {
                                    classes.push('new');
                                }
                                if ($.inArray(thisYear, steps) !== -1) {
                                    classes.push('active');
                                }
                                if (thisYear < startStep || thisYear > endStep) {
                                    classes.push('disabled');
                                }
                                if (thisYear === this.viewDate.getFullYear()) {
                                    classes.push('focused');
                                }

                                if (callback !== $.noop) {
                                    before = callback(new Date(thisYear, 0, 1));
                                    if (before === undefined) {
                                        before = {};
                                    } else if (typeof before === 'boolean') {
                                        before = {enabled: before};
                                    } else if (typeof before === 'string') {
                                        before = {classes: before};
                                    }
                                    if (before.enabled === false) {
                                        classes.push('disabled');
                                    }
                                    if (before.classes) {
                                        classes = classes.concat(before.classes.split(/\s+/));
                                    }
                                    if (before.tooltip) {
                                        tooltip = before.tooltip;
                                    }
                                }

                                html += '<span class="' + classes.join(' ') + '"' + (tooltip ? ' title="' + tooltip + '"' : '') + '>' + thisYear + '</span>';
                                thisYear += step;
                            }
                            view.find('td').html(html);
                        },
                        fill: function () {
                            var d = new Date(this.viewDate),
                                    year = d.getUTCFullYear(),
                                    month = d.getUTCMonth(),
                                    startYear = this.o.startDate !== -Infinity ? this.o.startDate.getUTCFullYear() : -Infinity,
                                    startMonth = this.o.startDate !== -Infinity ? this.o.startDate.getUTCMonth() : -Infinity,
                                    endYear = this.o.endDate !== Infinity ? this.o.endDate.getUTCFullYear() : Infinity,
                                    endMonth = this.o.endDate !== Infinity ? this.o.endDate.getUTCMonth() : Infinity,
                                    todaytxt = dates[this.o.language].today || dates['en'].today || '',
                                    cleartxt = dates[this.o.language].clear || dates['en'].clear || '',
                                    titleFormat = dates[this.o.language].titleFormat || dates['en'].titleFormat,
                                    tooltip,
                                    before;
                            if (isNaN(year) || isNaN(month))
                                return;
                            this.picker.find('.datepicker-days .datepicker-switch')
                                    .text(DPGlobal.formatDate(d, titleFormat, this.o.language));
                            this.picker.find('tfoot .today')
                                    .text(todaytxt)
                                    .toggle(this.o.todayBtn !== false);
                            this.picker.find('tfoot .clear')
                                    .text(cleartxt)
                                    .toggle(this.o.clearBtn !== false);
                            this.picker.find('thead .datepicker-title')
                                    .text(this.o.title)
                                    .toggle(this.o.title !== '');
                            this.updateNavArrows();
                            this.fillMonths();
                            var prevMonth = UTCDate(year, month, 0),
                                    day = prevMonth.getUTCDate();
                            prevMonth.setUTCDate(day - (prevMonth.getUTCDay() - this.o.weekStart + 7) % 7);
                            var nextMonth = new Date(prevMonth);
                            if (prevMonth.getUTCFullYear() < 100) {
                                nextMonth.setUTCFullYear(prevMonth.getUTCFullYear());
                            }
                            nextMonth.setUTCDate(nextMonth.getUTCDate() + 42);
                            nextMonth = nextMonth.valueOf();
                            var html = [];
                            var weekDay, clsName;
                            while (prevMonth.valueOf() < nextMonth) {
                                weekDay = prevMonth.getUTCDay();
                                if (weekDay === this.o.weekStart) {
                                    html.push('<tr>');
                                    if (this.o.calendarWeeks) {
                                        // ISO 8601: First week contains first thursday.
                                        // ISO also states week starts on Monday, but we can be more abstract here.
                                        var
                                                // Start of current week: based on weekstart/current date
                                                ws = new Date(+prevMonth + (this.o.weekStart - weekDay - 7) % 7 * 864e5),
                                                // Thursday of this week
                                                th = new Date(Number(ws) + (7 + 4 - ws.getUTCDay()) % 7 * 864e5),
                                                // First Thursday of year, year from thursday
                                                yth = new Date(Number(yth = UTCDate(th.getUTCFullYear(), 0, 1)) + (7 + 4 - yth.getUTCDay()) % 7 * 864e5),
                                                // Calendar week: ms between thursdays, div ms per day, div 7 days
                                                calWeek = (th - yth) / 864e5 / 7 + 1;
                                        html.push('<td class="cw">' + calWeek + '</td>');
                                    }
                                }
                                clsName = this.getClassNames(prevMonth);
                                clsName.push('day');

                                if (this.o.beforeShowDay !== $.noop) {
                                    before = this.o.beforeShowDay(this._utc_to_local(prevMonth));
                                    if (before === undefined)
                                        before = {};
                                    else if (typeof before === 'boolean')
                                        before = {enabled: before};
                                    else if (typeof before === 'string')
                                        before = {classes: before};
                                    if (before.enabled === false)
                                        clsName.push('disabled');
                                    if (before.classes)
                                        clsName = clsName.concat(before.classes.split(/\s+/));
                                    if (before.tooltip)
                                        tooltip = before.tooltip;
                                }

                                //Check if uniqueSort exists (supported by jquery >=1.12 and >=2.2)
                                //Fallback to unique function for older jquery versions
                                if ($.isFunction($.uniqueSort)) {
                                    clsName = $.uniqueSort(clsName);
                                } else {
                                    clsName = $.unique(clsName);
                                }

                                html.push('<td class="' + clsName.join(' ') + '"' + (tooltip ? ' title="' + tooltip + '"' : '') + (this.o.dateCells ? ' data-date="' + (prevMonth.getTime().toString()) + '"' : '') + '>' + prevMonth.getUTCDate() + '</td>');
                                tooltip = null;
                                if (weekDay === this.o.weekEnd) {
                                    html.push('</tr>');
                                }
                                prevMonth.setUTCDate(prevMonth.getUTCDate() + 1);
                            }
                            this.picker.find('.datepicker-days tbody').html(html.join(''));

                            var monthsTitle = dates[this.o.language].monthsTitle || dates['en'].monthsTitle || 'Months';
                            var months = this.picker.find('.datepicker-months')
                                    .find('.datepicker-switch')
                                    .text(this.o.maxViewMode < 2 ? monthsTitle : year)
                                    .end()
                                    .find('tbody span').removeClass('active');

                            $.each(this.dates, function (i, d) {
                                if (d.getUTCFullYear() === year)
                                    months.eq(d.getUTCMonth()).addClass('active');
                            });

                            if (year < startYear || year > endYear) {
                                months.addClass('disabled');
                            }
                            if (year === startYear) {
                                months.slice(0, startMonth).addClass('disabled');
                            }
                            if (year === endYear) {
                                months.slice(endMonth + 1).addClass('disabled');
                            }

                            if (this.o.beforeShowMonth !== $.noop) {
                                var that = this;
                                $.each(months, function (i, month) {
                                    var moDate = new Date(year, i, 1);
                                    var before = that.o.beforeShowMonth(moDate);
                                    if (before === undefined)
                                        before = {};
                                    else if (typeof before === 'boolean')
                                        before = {enabled: before};
                                    else if (typeof before === 'string')
                                        before = {classes: before};
                                    if (before.enabled === false && !$(month).hasClass('disabled'))
                                        $(month).addClass('disabled');
                                    if (before.classes)
                                        $(month).addClass(before.classes);
                                    if (before.tooltip)
                                        $(month).prop('title', before.tooltip);
                                });
                            }

                            // Generating decade/years picker
                            this._fill_yearsView(
                                    '.datepicker-years',
                                    'year',
                                    10,
                                    1,
                                    year,
                                    startYear,
                                    endYear,
                                    this.o.beforeShowYear
                                    );

                            // Generating century/decades picker
                            this._fill_yearsView(
                                    '.datepicker-decades',
                                    'decade',
                                    100,
                                    10,
                                    year,
                                    startYear,
                                    endYear,
                                    this.o.beforeShowDecade
                                    );

                            // Generating millennium/centuries picker
                            this._fill_yearsView(
                                    '.datepicker-centuries',
                                    'century',
                                    1000,
                                    100,
                                    year,
                                    startYear,
                                    endYear,
                                    this.o.beforeShowCentury
                                    );
                        },
                        updateNavArrows: function () {
                            if (!this._allow_update)
                                return;

                            var d = new Date(this.viewDate),
                                    year = d.getUTCFullYear(),
                                    month = d.getUTCMonth(),
                                    prevState,
                                    nextState;
                            switch (this.viewMode) {
                                case 0:
                                    prevState = (
                                            this.o.startDate !== -Infinity &&
                                            year <= this.o.startDate.getUTCFullYear() &&
                                            month <= this.o.startDate.getUTCMonth()
                                            );

                                    nextState = (
                                            this.o.endDate !== Infinity &&
                                            year >= this.o.endDate.getUTCFullYear() &&
                                            month >= this.o.endDate.getUTCMonth()
                                            );
                                    break;
                                case 1:
                                case 2:
                                case 3:
                                case 4:
                                    prevState = (
                                            this.o.startDate !== -Infinity &&
                                            year <= this.o.startDate.getUTCFullYear()
                                            );

                                    nextState = (
                                            this.o.endDate !== Infinity &&
                                            year >= this.o.endDate.getUTCFullYear()
                                            );
                                    break;
                            }

                            this.picker.find('.prev').toggleClass('disabled', prevState);
                            this.picker.find('.next').toggleClass('disabled', nextState);
                        },
                        click: function (e) {
                            e.preventDefault();
                            e.stopPropagation();

                            var target, dir, day, year, month;
                            target = $(e.target);

                            // Clicked on the switch
                            if (target.hasClass('datepicker-switch') && this.viewMode !== this.o.maxViewMode) {
                                this.setViewMode(this.viewMode + 1);
                            }

                            // Clicked on today button
                            if (target.hasClass('today') && !target.hasClass('day')) {
                                this.setViewMode(0);
                                this._setDate(UTCToday(), this.o.todayBtn === 'linked' ? null : 'view');
                            }

                            // Clicked on clear button
                            if (target.hasClass('clear')) {
                                this.clearDates();
                            }

                            if (!target.hasClass('disabled')) {
                                // Clicked on a day
                                if (target.hasClass('day')) {
                                    day = Number(target.text());
                                    year = this.viewDate.getUTCFullYear();
                                    month = this.viewDate.getUTCMonth();

                                    if (target.hasClass('old') || target.hasClass('new')) {
                                        dir = target.hasClass('old') ? -1 : 1;
                                        month = (month + dir + 12) % 12;
                                        if ((dir === -1 && month === 11) || (dir === 1 && month === 0)) {
                                            year += dir;
                                            this._trigger('changeYear', this.viewDate);
                                        }
                                        this._trigger('changeMonth', this.viewDate);
                                    }
                                    this._setDate(UTCDate(year, month, day));
                                }

                                // Clicked on a month, year, decade, century
                                if (target.hasClass('month')
                                        || target.hasClass('year')
                                        || target.hasClass('decade')
                                        || target.hasClass('century')) {
                                    this.viewDate.setUTCDate(1);

                                    day = 1;
                                    if (this.viewMode === 1) {
                                        month = target.parent().find('span').index(target);
                                        year = this.viewDate.getUTCFullYear();
                                        this.viewDate.setUTCMonth(month);
                                    } else {
                                        month = 0;
                                        year = Number(target.text());
                                        this.viewDate.setUTCFullYear(year);
                                    }

                                    this._trigger(DPGlobal.viewModes[this.viewMode - 1].e, this.viewDate);

                                    if (this.viewMode === this.o.minViewMode) {
                                        this._setDate(UTCDate(year, month, day));
                                    } else {
                                        this.setViewMode(this.viewMode - 1);
                                        this.fill();
                                    }
                                }
                            }

                            if (this.picker.is(':visible') && this._focused_from) {
                                this._focused_from.focus();
                            }
                            delete this._focused_from;
                        },
                        // Clicked on prev or next
                        navArrowsClick: function (e) {
                            var target = $(e.target);
                            var dir = target.hasClass('prev') ? -1 : 1;
                            if (this.viewMode !== 0) {
                                dir *= DPGlobal.viewModes[this.viewMode].navStep * 12;
                            }
                            this.viewDate = this.moveMonth(this.viewDate, dir);
                            this._trigger(DPGlobal.viewModes[this.viewMode].e, this.viewDate);
                            this.fill();
                        },
                        _toggle_multidate: function (date) {
                            var ix = this.dates.contains(date);
                            if (!date) {
                                this.dates.clear();
                            }

                            if (ix !== -1) {
                                if (this.o.multidate === true || this.o.multidate > 1 || this.o.toggleActive) {
                                    this.dates.remove(ix);
                                }
                            } else if (this.o.multidate === false) {
                                this.dates.clear();
                                this.dates.push(date);
                            }
                            else {
                                this.dates.push(date);
                            }

                            if (typeof this.o.multidate === 'number')
                                while (this.dates.length > this.o.multidate)
                                    this.dates.remove(0);
                        },
                        _setDate: function (date, which) {
                            if (!which || which === 'date')
                                this._toggle_multidate(date && new Date(date));
                            if (!which || which === 'view')
                                this.viewDate = date && new Date(date);

                            this.fill();
                            this.setValue();
                            if (!which || which !== 'view') {
                                this._trigger('changeDate');
                            }
                            this.inputField.trigger('change');
                            if (this.o.autoclose && (!which || which === 'date')) {
                                this.hide();
                            }
                        },
                        moveDay: function (date, dir) {
                            var newDate = new Date(date);
                            newDate.setUTCDate(date.getUTCDate() + dir);

                            return newDate;
                        },
                        moveWeek: function (date, dir) {
                            return this.moveDay(date, dir * 7);
                        },
                        moveMonth: function (date, dir) {
                            if (!isValidDate(date))
                                return this.o.defaultViewDate;
                            if (!dir)
                                return date;
                            var new_date = new Date(date.valueOf()),
                                    day = new_date.getUTCDate(),
                                    month = new_date.getUTCMonth(),
                                    mag = Math.abs(dir),
                                    new_month, test;
                            dir = dir > 0 ? 1 : -1;
                            if (mag === 1) {
                                test = dir === -1
                                        // If going back one month, make sure month is not current month
                                        // (eg, Mar 31 -> Feb 31 == Feb 28, not Mar 02)
                                        ? function () {
                                            return new_date.getUTCMonth() === month;
                                        }
                                // If going forward one month, make sure month is as expected
                                // (eg, Jan 31 -> Feb 31 == Feb 28, not Mar 02)
                                : function () {
                                    return new_date.getUTCMonth() !== new_month;
                                };
                                new_month = month + dir;
                                new_date.setUTCMonth(new_month);
                                // Dec -> Jan (12) or Jan -> Dec (-1) -- limit expected date to 0-11
                                new_month = (new_month + 12) % 12;
                            }
                            else {
                                // For magnitudes >1, move one month at a time...
                                for (var i = 0; i < mag; i++)
                                    // ...which might decrease the day (eg, Jan 31 to Feb 28, etc)...
                                    new_date = this.moveMonth(new_date, dir);
                                // ...then reset the day, keeping it in the new month
                                new_month = new_date.getUTCMonth();
                                new_date.setUTCDate(day);
                                test = function () {
                                    return new_month !== new_date.getUTCMonth();
                                };
                            }
                            // Common date-resetting loop -- if date is beyond end of month, make it
                            // end of month
                            while (test()) {
                                new_date.setUTCDate(--day);
                                new_date.setUTCMonth(new_month);
                            }
                            return new_date;
                        },
                        moveYear: function (date, dir) {
                            return this.moveMonth(date, dir * 12);
                        },
                        moveAvailableDate: function (date, dir, fn) {
                            do {
                                date = this[fn](date, dir);

                                if (!this.dateWithinRange(date))
                                    return false;

                                fn = 'moveDay';
                            }
                            while (this.dateIsDisabled(date));

                            return date;
                        },
                        weekOfDateIsDisabled: function (date) {
                            return $.inArray(date.getUTCDay(), this.o.daysOfWeekDisabled) !== -1;
                        },
                        dateIsDisabled: function (date) {
                            return (
                                    this.weekOfDateIsDisabled(date) ||
                                    $.grep(this.o.datesDisabled, function (d) {
                                        return isUTCEquals(date, d);
                                    }).length > 0
                                    );
                        },
                        dateWithinRange: function (date) {
                            return date >= this.o.startDate && date <= this.o.endDate;
                        },
                        keydown: function (e) {
                            if (!this.picker.is(':visible')) {
                                if (e.keyCode === 40 || e.keyCode === 27) { // allow down to re-show picker
                                    this.show();
                                    e.stopPropagation();
                                }
                                return;
                            }
                            var dateChanged = false,
                                    dir, newViewDate,
                                    focusDate = this.focusDate || this.viewDate;
                            switch (e.keyCode) {
                                case 27: // escape
                                    if (this.focusDate) {
                                        this.focusDate = null;
                                        this.viewDate = this.dates.get(-1) || this.viewDate;
                                        this.fill();
                                    }
                                    else
                                        this.hide();
                                    e.preventDefault();
                                    e.stopPropagation();
                                    break;
                                case 37: // left
                                case 38: // up
                                case 39: // right
                                case 40: // down
                                    if (!this.o.keyboardNavigation || this.o.daysOfWeekDisabled.length === 7)
                                        break;
                                    dir = e.keyCode === 37 || e.keyCode === 38 ? -1 : 1;
                                    if (this.viewMode === 0) {
                                        if (e.ctrlKey) {
                                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveYear');

                                            if (newViewDate)
                                                this._trigger('changeYear', this.viewDate);
                                        } else if (e.shiftKey) {
                                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveMonth');

                                            if (newViewDate)
                                                this._trigger('changeMonth', this.viewDate);
                                        } else if (e.keyCode === 37 || e.keyCode === 39) {
                                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveDay');
                                        } else if (!this.weekOfDateIsDisabled(focusDate)) {
                                            newViewDate = this.moveAvailableDate(focusDate, dir, 'moveWeek');
                                        }
                                    } else if (this.viewMode === 1) {
                                        if (e.keyCode === 38 || e.keyCode === 40) {
                                            dir = dir * 4;
                                        }
                                        newViewDate = this.moveAvailableDate(focusDate, dir, 'moveMonth');
                                    } else if (this.viewMode === 2) {
                                        if (e.keyCode === 38 || e.keyCode === 40) {
                                            dir = dir * 4;
                                        }
                                        newViewDate = this.moveAvailableDate(focusDate, dir, 'moveYear');
                                    }
                                    if (newViewDate) {
                                        this.focusDate = this.viewDate = newViewDate;
                                        this.setValue();
                                        this.fill();
                                        e.preventDefault();
                                    }
                                    break;
                                case 13: // enter
                                    if (!this.o.forceParse)
                                        break;
                                    focusDate = this.focusDate || this.dates.get(-1) || this.viewDate;
                                    if (this.o.keyboardNavigation) {
                                        this._toggle_multidate(focusDate);
                                        dateChanged = true;
                                    }
                                    this.focusDate = null;
                                    this.viewDate = this.dates.get(-1) || this.viewDate;
                                    this.setValue();
                                    this.fill();
                                    if (this.picker.is(':visible')) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        if (this.o.autoclose)
                                            this.hide();
                                    }
                                    break;
                                case 9: // tab
                                    this.focusDate = null;
                                    this.viewDate = this.dates.get(-1) || this.viewDate;
                                    this.fill();
                                    this.hide();
                                    break;
                            }
                            if (dateChanged) {
                                if (this.dates.length)
                                    this._trigger('changeDate');
                                else
                                    this._trigger('clearDate');
                                this.inputField.trigger('change');
                            }
                        },
                        setViewMode: function (viewMode) {
                            this.viewMode = viewMode;
                            this.picker
                                    .children('div')
                                    .hide()
                                    .filter('.datepicker-' + DPGlobal.viewModes[this.viewMode].clsName)
                                    .show();
                            this.updateNavArrows();
                            this._trigger('changeViewMode', new Date(this.viewDate));
                        }
                    };

                    var DateRangePicker = function (element, options) {
                        $.data(element, 'datepicker', this);
                        this.element = $(element);
                        this.inputs = $.map(options.inputs, function (i) {
                            return i.jquery ? i[0] : i;
                        });
                        delete options.inputs;

                        this.keepEmptyValues = options.keepEmptyValues;
                        delete options.keepEmptyValues;

                        datepickerPlugin.call($(this.inputs), options)
                                .on('changeDate', $.proxy(this.dateUpdated, this));

                        this.pickers = $.map(this.inputs, function (i) {
                            return $.data(i, 'datepicker');
                        });
                        this.updateDates();
                    };
                    DateRangePicker.prototype = {
                        updateDates: function () {
                            this.dates = $.map(this.pickers, function (i) {
                                return i.getUTCDate();
                            });
                            this.updateRanges();
                        },
                        updateRanges: function () {
                            var range = $.map(this.dates, function (d) {
                                return d.valueOf();
                            });
                            $.each(this.pickers, function (i, p) {
                                p.setRange(range);
                            });
                        },
                        dateUpdated: function (e) {
                            // `this.updating` is a workaround for preventing infinite recursion
                            // between `changeDate` triggering and `setUTCDate` calling.  Until
                            // there is a better mechanism.
                            if (this.updating)
                                return;
                            this.updating = true;

                            var dp = $.data(e.target, 'datepicker');

                            if (dp === undefined) {
                                return;
                            }

                            var new_date = dp.getUTCDate(),
                                    keep_empty_values = this.keepEmptyValues,
                                    i = $.inArray(e.target, this.inputs),
                                    j = i - 1,
                                    k = i + 1,
                                    l = this.inputs.length;
                            if (i === -1)
                                return;

                            $.each(this.pickers, function (i, p) {
                                if (!p.getUTCDate() && (p === dp || !keep_empty_values))
                                    p.setUTCDate(new_date);
                            });

                            if (new_date < this.dates[j]) {
                                // Date being moved earlier/left
                                while (j >= 0 && new_date < this.dates[j]) {
                                    this.pickers[j--].setUTCDate(new_date);
                                }
                            } else if (new_date > this.dates[k]) {
                                // Date being moved later/right
                                while (k < l && new_date > this.dates[k]) {
                                    this.pickers[k++].setUTCDate(new_date);
                                }
                            }
                            this.updateDates();

                            delete this.updating;
                        },
                        destroy: function () {
                            $.map(this.pickers, function (p) {
                                p.destroy();
                            });
                            $(this.inputs).off('changeDate', this.dateUpdated);
                            delete this.element.data().datepicker;
                        },
                        remove: alias('destroy')
                    };

                    function opts_from_el(el, prefix) {
                        // Derive options from element data-attrs
                        var data = $(el).data(),
                                out = {}, inkey,
                                replace = new RegExp('^' + prefix.toLowerCase() + '([A-Z])');
                        prefix = new RegExp('^' + prefix.toLowerCase());
                        function re_lower(_, a) {
                            return a.toLowerCase();
                        }
                        for (var key in data)
                            if (prefix.test(key)) {
                                inkey = key.replace(replace, re_lower);
                                out[inkey] = data[key];
                            }
                        return out;
                    }

                    function opts_from_locale(lang) {
                        // Derive options from locale plugins
                        var out = {};
                        // Check if "de-DE" style date is available, if not language should
                        // fallback to 2 letter code eg "de"
                        if (!dates[lang]) {
                            lang = lang.split('-')[0];
                            if (!dates[lang])
                                return;
                        }
                        var d = dates[lang];
                        $.each(locale_opts, function (i, k) {
                            if (k in d)
                                out[k] = d[k];
                        });
                        return out;
                    }

                    var old = $.fn.datepicker;
                    var datepickerPlugin = function (option) {
                        var args = Array.apply(null, arguments);
                        args.shift();
                        var internal_return;
                        this.each(function () {
                            var $this = $(this),
                                    data = $this.data('datepicker'),
                                    options = typeof option === 'object' && option;
                            if (!data) {
                                var elopts = opts_from_el(this, 'date'),
                                        // Preliminary otions
                                        xopts = $.extend({}, defaults, elopts, options),
                                        locopts = opts_from_locale(xopts.language),
                                        // Options priority: js args, data-attrs, locales, defaults
                                        opts = $.extend({}, defaults, locopts, elopts, options);
                                if ($this.hasClass('input-daterange') || opts.inputs) {
                                    $.extend(opts, {
                                        inputs: opts.inputs || $this.find('input').toArray()
                                    });
                                    data = new DateRangePicker(this, opts);
                                }
                                else {
                                    data = new Datepicker(this, opts);
                                }
                                $this.data('datepicker', data);
                            }
                            if (typeof option === 'string' && typeof data[option] === 'function') {
                                internal_return = data[option].apply(data, args);
                            }
                        });

                        if (
                                internal_return === undefined ||
                                internal_return instanceof Datepicker ||
                                internal_return instanceof DateRangePicker
                                )
                            return this;

                        if (this.length > 1)
                            throw new Error('Using only allowed for the collection of a single element (' + option + ' function)');
                        else
                            return internal_return;
                    };
                    $.fn.datepicker = datepickerPlugin;

                    var defaults = $.fn.datepicker.defaults = {
                        assumeNearbyYear: false,
                        autoclose: false,
                        beforeShowDay: $.noop,
                        beforeShowMonth: $.noop,
                        beforeShowYear: $.noop,
                        beforeShowDecade: $.noop,
                        beforeShowCentury: $.noop,
                        calendarWeeks: false,
                        clearBtn: false,
                        toggleActive: false,
                        daysOfWeekDisabled: [],
                        daysOfWeekHighlighted: [],
                        datesDisabled: [],
                        endDate: Infinity,
                        forceParse: true,
                        format: 'mm/dd/yyyy',
                        keepEmptyValues: false,
                        keyboardNavigation: true,
                        language: 'en',
                        minViewMode: 0,
                        maxViewMode: 4,
                        multidate: false,
                        multidateSeparator: ',',
                        orientation: "auto",
                        rtl: false,
                        startDate: -Infinity,
                        startView: 0,
                        todayBtn: false,
                        todayHighlight: false,
                        weekStart: 0,
                        disableTouchKeyboard: false,
                        enableOnReadonly: true,
                        showOnFocus: true,
                        zIndexOffset: 10,
                        container: 'body',
                        immediateUpdates: false,
                        dateCells: false,
                        title: '',
                        templates: {
                            leftArrow: '&laquo;',
                            rightArrow: '&raquo;'
                        }
                    };
                    var locale_opts = $.fn.datepicker.locale_opts = [
                        'format',
                        'rtl',
                        'weekStart'
                    ];
                    $.fn.datepicker.Constructor = Datepicker;
                    var dates = $.fn.datepicker.dates = {
                        en: {
                            days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                            daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
                            daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
                            months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
                            monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                            today: "Today",
                            clear: "Clear",
                            titleFormat: "MM yyyy"
                        }
                    };

                    var DPGlobal = {
                        viewModes: [
                            {
                                names: ['days', 'month'],
                                clsName: 'days',
                                e: 'changeMonth'
                            },
                            {
                                names: ['months', 'year'],
                                clsName: 'months',
                                e: 'changeYear',
                                navStep: 1
                            },
                            {
                                names: ['years', 'decade'],
                                clsName: 'years',
                                e: 'changeDecade',
                                navStep: 10
                            },
                            {
                                names: ['decades', 'century'],
                                clsName: 'decades',
                                e: 'changeCentury',
                                navStep: 100
                            },
                            {
                                names: ['centuries', 'millennium'],
                                clsName: 'centuries',
                                e: 'changeMillennium',
                                navStep: 1000
                            }
                        ],
                        validParts: /dd?|DD?|mm?|MM?|yy(?:yy)?/g,
                        nonpunctuation: /[^ -\/:-@\u5e74\u6708\u65e5\[-`{-~\t\n\r]+/g,
                        parseFormat: function (format) {
                            if (typeof format.toValue === 'function' && typeof format.toDisplay === 'function')
                                return format;
                            // IE treats \0 as a string end in inputs (truncating the value),
                            // so it's a bad format delimiter, anyway
                            var separators = format.replace(this.validParts, '\0').split('\0'),
                                    parts = format.match(this.validParts);
                            if (!separators || !separators.length || !parts || parts.length === 0) {
                                throw new Error("Invalid date format.");
                            }
                            return {separators: separators, parts: parts};
                        },
                        parseDate: function (date, format, language, assumeNearby) {
                            if (!date)
                                return undefined;
                            if (date instanceof Date)
                                return date;
                            if (typeof format === 'string')
                                format = DPGlobal.parseFormat(format);
                            if (format.toValue)
                                return format.toValue(date, format, language);
                            var fn_map = {
                                d: 'moveDay',
                                m: 'moveMonth',
                                w: 'moveWeek',
                                y: 'moveYear'
                            },
                            dateAliases = {
                                yesterday: '-1d',
                                today: '+0d',
                                tomorrow: '+1d'
                            },
                            parts, part, dir, i, fn;
                            if (date in dateAliases) {
                                date = dateAliases[date];
                            }
                            if (/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/i.test(date)) {
                                parts = date.match(/([\-+]\d+)([dmwy])/gi);
                                date = new Date();
                                for (i = 0; i < parts.length; i++) {
                                    part = parts[i].match(/([\-+]\d+)([dmwy])/i);
                                    dir = Number(part[1]);
                                    fn = fn_map[part[2].toLowerCase()];
                                    date = Datepicker.prototype[fn](date, dir);
                                }
                                return UTCDate(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate());
                            }

                            parts = date && date.match(this.nonpunctuation) || [];
                            date = new Date();

                            function applyNearbyYear(year, threshold) {
                                if (threshold === true)
                                    threshold = 10;

                                // if year is 2 digits or less, than the user most likely is trying to get a recent century
                                if (year < 100) {
                                    year += 2000;
                                    // if the new year is more than threshold years in advance, use last century
                                    if (year > ((new Date()).getFullYear() + threshold)) {
                                        year -= 100;
                                    }
                                }

                                return year;
                            }

                            var parsed = {},
                                    setters_order = ['yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'd', 'dd'],
                                    setters_map = {
                                        yyyy: function (d, v) {
                                            return d.setUTCFullYear(assumeNearby ? applyNearbyYear(v, assumeNearby) : v);
                                        },
                                        m: function (d, v) {
                                            if (isNaN(d))
                                                return d;
                                            v -= 1;
                                            while (v < 0)
                                                v += 12;
                                            v %= 12;
                                            d.setUTCMonth(v);
                                            while (d.getUTCMonth() !== v)
                                                d.setUTCDate(d.getUTCDate() - 1);
                                            return d;
                                        },
                                        d: function (d, v) {
                                            return d.setUTCDate(v);
                                        }
                                    },
                            val, filtered;
                            setters_map['yy'] = setters_map['yyyy'];
                            setters_map['M'] = setters_map['MM'] = setters_map['mm'] = setters_map['m'];
                            setters_map['dd'] = setters_map['d'];
                            date = UTCToday();
                            var fparts = format.parts.slice();
                            // Remove noop parts
                            if (parts.length !== fparts.length) {
                                fparts = $(fparts).filter(function (i, p) {
                                    return $.inArray(p, setters_order) !== -1;
                                }).toArray();
                            }
                            // Process remainder
                            function match_part() {
                                var m = this.slice(0, parts[i].length),
                                        p = parts[i].slice(0, m.length);
                                return m.toLowerCase() === p.toLowerCase();
                            }
                            if (parts.length === fparts.length) {
                                var cnt;
                                for (i = 0, cnt = fparts.length; i < cnt; i++) {
                                    val = parseInt(parts[i], 10);
                                    part = fparts[i];
                                    if (isNaN(val)) {
                                        switch (part) {
                                            case 'MM':
                                                filtered = $(dates[language].months).filter(match_part);
                                                val = $.inArray(filtered[0], dates[language].months) + 1;
                                                break;
                                            case 'M':
                                                filtered = $(dates[language].monthsShort).filter(match_part);
                                                val = $.inArray(filtered[0], dates[language].monthsShort) + 1;
                                                break;
                                        }
                                    }
                                    parsed[part] = val;
                                }
                                var _date, s;
                                for (i = 0; i < setters_order.length; i++) {
                                    s = setters_order[i];
                                    if (s in parsed && !isNaN(parsed[s])) {
                                        _date = new Date(date);
                                        setters_map[s](_date, parsed[s]);
                                        if (!isNaN(_date))
                                            date = _date;
                                    }
                                }
                            }
                            return date;
                        },
                        formatDate: function (date, format, language) {
                            if (!date)
                                return '';
                            if (typeof format === 'string')
                                format = DPGlobal.parseFormat(format);
                            if (format.toDisplay)
                                return format.toDisplay(date, format, language);
                            var val = {
                                d: date.getUTCDate(),
                                D: dates[language].daysShort[date.getUTCDay()],
                                DD: dates[language].days[date.getUTCDay()],
                                m: date.getUTCMonth() + 1,
                                M: dates[language].monthsShort[date.getUTCMonth()],
                                MM: dates[language].months[date.getUTCMonth()],
                                yy: date.getUTCFullYear().toString().substring(2),
                                yyyy: date.getUTCFullYear()
                            };
                            val.dd = (val.d < 10 ? '0' : '') + val.d;
                            val.mm = (val.m < 10 ? '0' : '') + val.m;
                            date = [];
                            var seps = $.extend([], format.separators);
                            for (var i = 0, cnt = format.parts.length; i <= cnt; i++) {
                                if (seps.length)
                                    date.push(seps.shift());
                                date.push(val[format.parts[i]]);
                            }
                            return date.join('');
                        },
                        headTemplate: '<thead>' +
                                '<tr>' +
                                '<th colspan="7" class="datepicker-title"></th>' +
                                '</tr>' +
                                '<tr>' +
                                '<th class="prev">&laquo;</th>' +
                                '<th colspan="5" class="datepicker-switch"></th>' +
                                '<th class="next">&raquo;</th>' +
                                '</tr>' +
                                '</thead>',
                        contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
                        footTemplate: '<tfoot>' +
                                '<tr>' +
                                '<th colspan="7" class="today"></th>' +
                                '</tr>' +
                                '<tr>' +
                                '<th colspan="7" class="clear"></th>' +
                                '</tr>' +
                                '</tfoot>'
                    };
                    DPGlobal.template = '<div class="datepicker">' +
                            '<div class="datepicker-days">' +
                            '<table class="table-condensed">' +
                            DPGlobal.headTemplate +
                            '<tbody></tbody>' +
                            DPGlobal.footTemplate +
                            '</table>' +
                            '</div>' +
                            '<div class="datepicker-months">' +
                            '<table class="table-condensed">' +
                            DPGlobal.headTemplate +
                            DPGlobal.contTemplate +
                            DPGlobal.footTemplate +
                            '</table>' +
                            '</div>' +
                            '<div class="datepicker-years">' +
                            '<table class="table-condensed">' +
                            DPGlobal.headTemplate +
                            DPGlobal.contTemplate +
                            DPGlobal.footTemplate +
                            '</table>' +
                            '</div>' +
                            '<div class="datepicker-decades">' +
                            '<table class="table-condensed">' +
                            DPGlobal.headTemplate +
                            DPGlobal.contTemplate +
                            DPGlobal.footTemplate +
                            '</table>' +
                            '</div>' +
                            '<div class="datepicker-centuries">' +
                            '<table class="table-condensed">' +
                            DPGlobal.headTemplate +
                            DPGlobal.contTemplate +
                            DPGlobal.footTemplate +
                            '</table>' +
                            '</div>' +
                            '</div>';

                    $.fn.datepicker.DPGlobal = DPGlobal;


                    /* DATEPICKER NO CONFLICT
                     * =================== */

                    $.fn.datepicker.noConflict = function () {
                        $.fn.datepicker = old;
                        return this;
                    };

                    /* DATEPICKER VERSION
                     * =================== */
                    $.fn.datepicker.version = '1.7.0-dev';

                    /* DATEPICKER DATA-API
                     * ================== */

                    $(document).on(
                            'focus.datepicker.data-api click.datepicker.data-api',
                            '[data-provide="datepicker"]',
                            function (e) {
                                var $this = $(this);
                                if ($this.data('datepicker'))
                                    return;
                                e.preventDefault();
                                // component click requires us to explicitly show it
                                datepickerPlugin.call($this, 'show');
                            }
                    );
                    $(function () {
                        datepickerPlugin.call($('[data-provide="datepicker-inline"]'));
                    });

                }));

/* =========================================================
 * bootstrap-datetimepicker.js
 * =========================================================
 * Copyright 2012 Stefan Petre
 *
 * Improvements by Andrew Rowls
 * Improvements by Sébastien Malot
 * Improvements by Yun Lai
 * Improvements by Kenneth Henderick
 * Improvements by CuGBabyBeaR
 * Improvements by Christian Vaas <auspex@auspex.eu>
 *
 * Project URL : http://www.malot.fr/bootstrap-datetimepicker
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

(function(factory){
    if (typeof define === 'function' && define.amd)
      define(['jquery'], factory);
    else if (typeof exports === 'object')
      factory(require('jquery'));
    else
      factory(jQuery);

}(function($, undefined){

  // Add ECMA262-5 Array methods if not supported natively (IE8)
  if (!('indexOf' in Array.prototype)) {
    Array.prototype.indexOf = function (find, i) {
      if (i === undefined) i = 0;
      if (i < 0) i += this.length;
      if (i < 0) i = 0;
      for (var n = this.length; i < n; i++) {
        if (i in this && this[i] === find) {
          return i;
        }
      }
      return -1;
    }
  }

  // Add timezone abbreviation support for ie6+, Chrome, Firefox
  function timeZoneAbbreviation() {
    var abbreviation, date, formattedStr, i, len, matchedStrings, ref, str;
    date = (new Date()).toString();
    formattedStr = ((ref = date.split('(')[1]) != null ? ref.slice(0, -1) : 0) || date.split(' ');
    if (formattedStr instanceof Array) {
      matchedStrings = [];
      for (var i = 0, len = formattedStr.length; i < len; i++) {
        str = formattedStr[i];
        if ((abbreviation = (ref = str.match(/\b[A-Z]+\b/)) !== null) ? ref[0] : 0) {
          matchedStrings.push(abbreviation);
        }
      }
      formattedStr = matchedStrings.pop();
    }
    return formattedStr;
  }

  function UTCDate() {
    return new Date(Date.UTC.apply(Date, arguments));
  }

  // Picker object
  var Datetimepicker = function (element, options) {
    var that = this;

    this.element = $(element);

    // add container for single page application
    // when page switch the datetimepicker div will be removed also.
    this.container = options.container || 'body';

    this.language = options.language || this.element.data('date-language') || 'en';
    this.language = this.language in dates ? this.language : this.language.split('-')[0]; // fr-CA fallback to fr
    this.language = this.language in dates ? this.language : 'en';
    this.isRTL = dates[this.language].rtl || false;
    this.formatType = options.formatType || this.element.data('format-type') || 'standard';
    this.format = DPGlobal.parseFormat(options.format || this.element.data('date-format') || dates[this.language].format || DPGlobal.getDefaultFormat(this.formatType, 'input'), this.formatType);
    this.isInline = false;
    this.isVisible = false;
    this.isInput = this.element.is('input');
    this.fontAwesome = options.fontAwesome || this.element.data('font-awesome') || false;

    this.bootcssVer = options.bootcssVer || (this.isInput ? (this.element.is('.form-control') ? 3 : 2) : ( this.bootcssVer = this.element.is('.input-group') ? 3 : 2 ));

    this.component = this.element.is('.date') ? ( this.bootcssVer === 3 ? this.element.find('.input-group-addon .glyphicon-th, .input-group-addon .glyphicon-time, .input-group-addon .glyphicon-remove, .input-group-addon .glyphicon-calendar, .input-group-addon .fa-calendar, .input-group-addon .fa-clock-o').parent() : this.element.find('.add-on .icon-th, .add-on .icon-time, .add-on .icon-calendar, .add-on .fa-calendar, .add-on .fa-clock-o').parent()) : false;
    this.componentReset = this.element.is('.date') ? ( this.bootcssVer === 3 ? this.element.find('.input-group-addon .glyphicon-remove, .input-group-addon .fa-times').parent():this.element.find('.add-on .icon-remove, .add-on .fa-times').parent()) : false;
    this.hasInput = this.component && this.element.find('input').length;
    if (this.component && this.component.length === 0) {
      this.component = false;
    }
    this.linkField = options.linkField || this.element.data('link-field') || false;
    this.linkFormat = DPGlobal.parseFormat(options.linkFormat || this.element.data('link-format') || DPGlobal.getDefaultFormat(this.formatType, 'link'), this.formatType);
    this.minuteStep = options.minuteStep || this.element.data('minute-step') || 5;
    this.pickerPosition = options.pickerPosition || this.element.data('picker-position') || 'bottom-right';
    this.showMeridian = options.showMeridian || this.element.data('show-meridian') || false;
    this.initialDate = options.initialDate || new Date();
    this.zIndex = options.zIndex || this.element.data('z-index') || undefined;
    this.title = typeof options.title === 'undefined' ? false : options.title;
    this.timezone = options.timezone || timeZoneAbbreviation();

    this.icons = {
      leftArrow: this.fontAwesome ? 'fa-arrow-left' : (this.bootcssVer === 3 ? 'glyphicon-arrow-left' : 'icon-arrow-left'),
      rightArrow: this.fontAwesome ? 'fa-arrow-right' : (this.bootcssVer === 3 ? 'glyphicon-arrow-right' : 'icon-arrow-right')
    }
    this.icontype = this.fontAwesome ? 'fa' : 'glyphicon';

    this._attachEvents();

    this.clickedOutside = function (e) {
        // Clicked outside the datetimepicker, hide it
        if ($(e.target).closest('.datetimepicker').length === 0) {
            that.hide();
        }
    }

    this.formatViewType = 'datetime';
    if ('formatViewType' in options) {
      this.formatViewType = options.formatViewType;
    } else if ('formatViewType' in this.element.data()) {
      this.formatViewType = this.element.data('formatViewType');
    }

    this.minView = 0;
    if ('minView' in options) {
      this.minView = options.minView;
    } else if ('minView' in this.element.data()) {
      this.minView = this.element.data('min-view');
    }
    this.minView = DPGlobal.convertViewMode(this.minView);

    this.maxView = DPGlobal.modes.length - 1;
    if ('maxView' in options) {
      this.maxView = options.maxView;
    } else if ('maxView' in this.element.data()) {
      this.maxView = this.element.data('max-view');
    }
    this.maxView = DPGlobal.convertViewMode(this.maxView);

    this.wheelViewModeNavigation = false;
    if ('wheelViewModeNavigation' in options) {
      this.wheelViewModeNavigation = options.wheelViewModeNavigation;
    } else if ('wheelViewModeNavigation' in this.element.data()) {
      this.wheelViewModeNavigation = this.element.data('view-mode-wheel-navigation');
    }

    this.wheelViewModeNavigationInverseDirection = false;

    if ('wheelViewModeNavigationInverseDirection' in options) {
      this.wheelViewModeNavigationInverseDirection = options.wheelViewModeNavigationInverseDirection;
    } else if ('wheelViewModeNavigationInverseDirection' in this.element.data()) {
      this.wheelViewModeNavigationInverseDirection = this.element.data('view-mode-wheel-navigation-inverse-dir');
    }

    this.wheelViewModeNavigationDelay = 100;
    if ('wheelViewModeNavigationDelay' in options) {
      this.wheelViewModeNavigationDelay = options.wheelViewModeNavigationDelay;
    } else if ('wheelViewModeNavigationDelay' in this.element.data()) {
      this.wheelViewModeNavigationDelay = this.element.data('view-mode-wheel-navigation-delay');
    }

    this.startViewMode = 2;
    if ('startView' in options) {
      this.startViewMode = options.startView;
    } else if ('startView' in this.element.data()) {
      this.startViewMode = this.element.data('start-view');
    }
    this.startViewMode = DPGlobal.convertViewMode(this.startViewMode);
    this.viewMode = this.startViewMode;

    this.viewSelect = this.minView;
    if ('viewSelect' in options) {
      this.viewSelect = options.viewSelect;
    } else if ('viewSelect' in this.element.data()) {
      this.viewSelect = this.element.data('view-select');
    }
    this.viewSelect = DPGlobal.convertViewMode(this.viewSelect);

    this.forceParse = true;
    if ('forceParse' in options) {
      this.forceParse = options.forceParse;
    } else if ('dateForceParse' in this.element.data()) {
      this.forceParse = this.element.data('date-force-parse');
    }
    var template = this.bootcssVer === 3 ? DPGlobal.templateV3 : DPGlobal.template;
    while (template.indexOf('{iconType}') !== -1) {
      template = template.replace('{iconType}', this.icontype);
    }
    while (template.indexOf('{leftArrow}') !== -1) {
      template = template.replace('{leftArrow}', this.icons.leftArrow);
    }
    while (template.indexOf('{rightArrow}') !== -1) {
      template = template.replace('{rightArrow}', this.icons.rightArrow);
    }
    this.picker = $(template)
      .appendTo(this.isInline ? this.element : this.container) // 'body')
      .on({
        click:     $.proxy(this.click, this),
        mousedown: $.proxy(this.mousedown, this)
      });

    if (this.wheelViewModeNavigation) {
      if ($.fn.mousewheel) {
        this.picker.on({mousewheel: $.proxy(this.mousewheel, this)});
      } else {
        console.log('Mouse Wheel event is not supported. Please include the jQuery Mouse Wheel plugin before enabling this option');
      }
    }

    if (this.isInline) {
      this.picker.addClass('datetimepicker-inline');
    } else {
      this.picker.addClass('datetimepicker-dropdown-' + this.pickerPosition + ' dropdown-menu');
    }
    if (this.isRTL) {
      this.picker.addClass('datetimepicker-rtl');
      var selector = this.bootcssVer === 3 ? '.prev span, .next span' : '.prev i, .next i';
      this.picker.find(selector).toggleClass(this.icons.leftArrow + ' ' + this.icons.rightArrow);
    }

    $(document).on('mousedown touchend', this.clickedOutside);

    this.autoclose = false;
    if ('autoclose' in options) {
      this.autoclose = options.autoclose;
    } else if ('dateAutoclose' in this.element.data()) {
      this.autoclose = this.element.data('date-autoclose');
    }

    this.keyboardNavigation = true;
    if ('keyboardNavigation' in options) {
      this.keyboardNavigation = options.keyboardNavigation;
    } else if ('dateKeyboardNavigation' in this.element.data()) {
      this.keyboardNavigation = this.element.data('date-keyboard-navigation');
    }

    this.todayBtn = (options.todayBtn || this.element.data('date-today-btn') || false);
    this.clearBtn = (options.clearBtn || this.element.data('date-clear-btn') || false);
    this.todayHighlight = (options.todayHighlight || this.element.data('date-today-highlight') || false);

    this.weekStart = 0;
    if (typeof options.weekStart !== 'undefined') {
      this.weekStart = options.weekStart;
    } else if (typeof this.element.data('date-weekstart') !== 'undefined') {
      this.weekStart = this.element.data('date-weekstart');
    } else if (typeof dates[this.language].weekStart !== 'undefined') {
      this.weekStart = dates[this.language].weekStart;
    }
    this.weekStart = this.weekStart % 7;
    this.weekEnd = ((this.weekStart + 6) % 7);
    this.onRenderDay = function (date) {
      var render = (options.onRenderDay || function () { return []; })(date);
      if (typeof render === 'string') {
        render = [render];
      }
      var res = ['day'];
      return res.concat((render ? render : []));
    };
    this.onRenderHour = function (date) {
      var render = (options.onRenderHour || function () { return []; })(date);
      var res = ['hour'];
      if (typeof render === 'string') {
        render = [render];
      }
      return res.concat((render ? render : []));
    };
    this.onRenderMinute = function (date) {
      var render = (options.onRenderMinute || function () { return []; })(date);
      var res = ['minute'];
      if (typeof render === 'string') {
        render = [render];
      }
      if (date < this.startDate || date > this.endDate) {
        res.push('disabled');
      } else if (Math.floor(this.date.getUTCMinutes() / this.minuteStep) === Math.floor(date.getUTCMinutes() / this.minuteStep)) {
        res.push('active');
      }
      return res.concat((render ? render : []));
    };
    this.onRenderYear = function (date) {
      var render = (options.onRenderYear || function () { return []; })(date);
      var res = ['year'];
      if (typeof render === 'string') {
        render = [render];
      }
      if (this.date.getUTCFullYear() === date.getUTCFullYear()) {
        res.push('active');
      }
      var currentYear = date.getUTCFullYear();
      var endYear = this.endDate.getUTCFullYear();
      if (date < this.startDate || currentYear > endYear) {
        res.push('disabled');
      }
      return res.concat((render ? render : []));
    }
    this.onRenderMonth = function (date) {
      var render = (options.onRenderMonth || function () { return []; })(date);
      var res = ['month'];
      if (typeof render === 'string') {
        render = [render];
      }
      return res.concat((render ? render : []));
    }
    this.startDate = new Date(-8639968443048000);
    this.endDate = new Date(8639968443048000);
    this.datesDisabled = [];
    this.daysOfWeekDisabled = [];
    this.setStartDate(options.startDate || this.element.data('date-startdate'));
    this.setEndDate(options.endDate || this.element.data('date-enddate'));
    this.setDatesDisabled(options.datesDisabled || this.element.data('date-dates-disabled'));
    this.setDaysOfWeekDisabled(options.daysOfWeekDisabled || this.element.data('date-days-of-week-disabled'));
    this.setMinutesDisabled(options.minutesDisabled || this.element.data('date-minute-disabled'));
    this.setHoursDisabled(options.hoursDisabled || this.element.data('date-hour-disabled'));
    this.fillDow();
    this.fillMonths();
    this.update();
    this.showMode();

    if (this.isInline) {
      this.show();
    }
  };

  Datetimepicker.prototype = {
    constructor: Datetimepicker,

    _events:       [],
    _attachEvents: function () {
      this._detachEvents();
      if (this.isInput) { // single input
        this._events = [
          [this.element, {
            focus:   $.proxy(this.show, this),
            keyup:   $.proxy(this.update, this),
            keydown: $.proxy(this.keydown, this)
          }]
        ];
      }
      else if (this.component && this.hasInput) { // component: input + button
        this._events = [
          // For components that are not readonly, allow keyboard nav
          [this.element.find('input'), {
            focus:   $.proxy(this.show, this),
            keyup:   $.proxy(this.update, this),
            keydown: $.proxy(this.keydown, this)
          }],
          [this.component, {
            click: $.proxy(this.show, this)
          }]
        ];
        if (this.componentReset) {
          this._events.push([
            this.componentReset,
            {click: $.proxy(this.reset, this)}
          ]);
        }
      }
      else if (this.element.is('div')) {  // inline datetimepicker
        this.isInline = true;
      }
      else {
        this._events = [
          [this.element, {
            click: $.proxy(this.show, this)
          }]
        ];
      }
      for (var i = 0, el, ev; i < this._events.length; i++) {
        el = this._events[i][0];
        ev = this._events[i][1];
        el.on(ev);
      }
    },

    _detachEvents: function () {
      for (var i = 0, el, ev; i < this._events.length; i++) {
        el = this._events[i][0];
        ev = this._events[i][1];
        el.off(ev);
      }
      this._events = [];
    },

    show: function (e) {
      this.picker.show();
      this.height = this.component ? this.component.outerHeight() : this.element.outerHeight();
      if (this.forceParse) {
        this.update();
      }
      this.place();
      $(window).on('resize', $.proxy(this.place, this));
      if (e) {
        e.stopPropagation();
        e.preventDefault();
      }
      this.isVisible = true;
      this.element.trigger({
        type: 'show',
        date: this.date
      });
    },

    hide: function () {
      if (!this.isVisible) return;
      if (this.isInline) return;
      this.picker.hide();
      $(window).off('resize', this.place);
      this.viewMode = this.startViewMode;
      this.showMode();
      if (!this.isInput) {
        $(document).off('mousedown', this.hide);
      }

      if (
        this.forceParse &&
          (
            this.isInput && this.element.val() ||
              this.hasInput && this.element.find('input').val()
            )
        )
        this.setValue();
      this.isVisible = false;
      this.element.trigger({
        type: 'hide',
        date: this.date
      });
    },

    remove: function () {
      this._detachEvents();
      $(document).off('mousedown', this.clickedOutside);
      this.picker.remove();
      delete this.picker;
      delete this.element.data().datetimepicker;
    },

    getDate: function () {
      var d = this.getUTCDate();
      if (d === null) {
        return null;
      }
      return new Date(d.getTime() + (d.getTimezoneOffset() * 60000));
    },

    getUTCDate: function () {
      return this.date;
    },

    getInitialDate: function () {
      return this.initialDate
    },

    setInitialDate: function (initialDate) {
      this.initialDate = initialDate;
    },

    setDate: function (d) {
      this.setUTCDate(new Date(d.getTime() - (d.getTimezoneOffset() * 60000)));
    },

    setUTCDate: function (d) {
      if (d >= this.startDate && d <= this.endDate) {
        this.date = d;
        this.setValue();
        this.viewDate = this.date;
        this.fill();
      } else {
        this.element.trigger({
          type:      'outOfRange',
          date:      d,
          startDate: this.startDate,
          endDate:   this.endDate
        });
      }
    },

    setFormat: function (format) {
      this.format = DPGlobal.parseFormat(format, this.formatType);
      var element;
      if (this.isInput) {
        element = this.element;
      } else if (this.component) {
        element = this.element.find('input');
      }
      if (element && element.val()) {
        this.setValue();
      }
    },

    setValue: function () {
      var formatted = this.getFormattedDate();
      if (!this.isInput) {
        if (this.component) {
          this.element.find('input').val(formatted);
        }
        this.element.data('date', formatted);
      } else {
        this.element.val(formatted);
      }
      if (this.linkField) {
        $('#' + this.linkField).val(this.getFormattedDate(this.linkFormat));
      }
    },

    getFormattedDate: function (format) {
      format = format || this.format;
      return DPGlobal.formatDate(this.date, format, this.language, this.formatType, this.timezone);
    },

    setStartDate: function (startDate) {
      this.startDate = startDate || this.startDate;
      if (this.startDate.valueOf() !== 8639968443048000) {
        this.startDate = DPGlobal.parseDate(this.startDate, this.format, this.language, this.formatType, this.timezone);
      }
      this.update();
      this.updateNavArrows();
    },

    setEndDate: function (endDate) {
      this.endDate = endDate || this.endDate;
      if (this.endDate.valueOf() !== 8639968443048000) {
        this.endDate = DPGlobal.parseDate(this.endDate, this.format, this.language, this.formatType, this.timezone);
      }
      this.update();
      this.updateNavArrows();
    },

    setDatesDisabled: function (datesDisabled) {
      this.datesDisabled = datesDisabled || [];
      if (!$.isArray(this.datesDisabled)) {
        this.datesDisabled = this.datesDisabled.split(/,\s*/);
      }
      var mThis = this;
      this.datesDisabled = $.map(this.datesDisabled, function (d) {
        return DPGlobal.parseDate(d, mThis.format, mThis.language, mThis.formatType, mThis.timezone).toDateString();
      });
      this.update();
      this.updateNavArrows();
    },

    setTitle: function (selector, value) {
      return this.picker.find(selector)
        .find('th:eq(1)')
        .text(this.title === false ? value : this.title);
    },

    setDaysOfWeekDisabled: function (daysOfWeekDisabled) {
      this.daysOfWeekDisabled = daysOfWeekDisabled || [];
      if (!$.isArray(this.daysOfWeekDisabled)) {
        this.daysOfWeekDisabled = this.daysOfWeekDisabled.split(/,\s*/);
      }
      this.daysOfWeekDisabled = $.map(this.daysOfWeekDisabled, function (d) {
        return parseInt(d, 10);
      });
      this.update();
      this.updateNavArrows();
    },

    setMinutesDisabled: function (minutesDisabled) {
      this.minutesDisabled = minutesDisabled || [];
      if (!$.isArray(this.minutesDisabled)) {
        this.minutesDisabled = this.minutesDisabled.split(/,\s*/);
      }
      this.minutesDisabled = $.map(this.minutesDisabled, function (d) {
        return parseInt(d, 10);
      });
      this.update();
      this.updateNavArrows();
    },

    setHoursDisabled: function (hoursDisabled) {
      this.hoursDisabled = hoursDisabled || [];
      if (!$.isArray(this.hoursDisabled)) {
        this.hoursDisabled = this.hoursDisabled.split(/,\s*/);
      }
      this.hoursDisabled = $.map(this.hoursDisabled, function (d) {
        return parseInt(d, 10);
      });
      this.update();
      this.updateNavArrows();
    },

    place: function () {
      if (this.isInline) return;

      if (!this.zIndex) {
        var index_highest = 0;
        $('div').each(function () {
          var index_current = parseInt($(this).css('zIndex'), 10);
          if (index_current > index_highest) {
            index_highest = index_current;
          }
        });
        this.zIndex = index_highest + 10;
      }

      var offset, top, left, containerOffset;
      if (this.container instanceof $) {
        containerOffset = this.container.offset();
      } else {
        containerOffset = $(this.container).offset();
      }

      if (this.component) {
        offset = this.component.offset();
        left = offset.left;
        if (this.pickerPosition === 'bottom-left' || this.pickerPosition === 'top-left') {
          left += this.component.outerWidth() - this.picker.outerWidth();
        }
      } else {
        offset = this.element.offset();
        left = offset.left;
        if (this.pickerPosition === 'bottom-left' || this.pickerPosition === 'top-left') {
          left += this.element.outerWidth() - this.picker.outerWidth();
        }
      }

      var bodyWidth = document.body.clientWidth || window.innerWidth;
      if (left + 220 > bodyWidth) {
        left = bodyWidth - 220;
      }

      if (this.pickerPosition === 'top-left' || this.pickerPosition === 'top-right') {
        top = offset.top - this.picker.outerHeight();
      } else {
        top = offset.top + this.height;
      }

      top = top - containerOffset.top;
      left = left - containerOffset.left;

      this.picker.css({
        top:    top,
        left:   left,
        zIndex: this.zIndex
      });
    },

    hour_minute: "^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]",

    update: function () {
      var date, fromArgs = false;
      if (arguments && arguments.length && (typeof arguments[0] === 'string' || arguments[0] instanceof Date)) {
        date = arguments[0];
        fromArgs = true;
      } else {
        date = (this.isInput ? this.element.val() : this.element.find('input').val()) || this.element.data('date') || this.initialDate;
        if (typeof date === 'string') {
          date = date.replace(/^\s+|\s+$/g,'');
        }
      }

      if (!date) {
        date = new Date();
        fromArgs = false;
      }

      if (typeof date === "string") {
        if (new RegExp(this.hour_minute).test(date) || new RegExp(this.hour_minute + ":[0-5][0-9]").test(date)) {
          date = this.getDate()
        }
      }

      this.date = DPGlobal.parseDate(date, this.format, this.language, this.formatType, this.timezone);

      if (fromArgs) this.setValue();

      if (this.date < this.startDate) {
        this.viewDate = new Date(this.startDate);
      } else if (this.date > this.endDate) {
        this.viewDate = new Date(this.endDate);
      } else {
        this.viewDate = new Date(this.date);
      }
      this.fill();
    },

    fillDow: function () {
      var dowCnt = this.weekStart,
        html = '<tr>';
      while (dowCnt < this.weekStart + 7) {
        html += '<th class="dow">' + dates[this.language].daysMin[(dowCnt++) % 7] + '</th>';
      }
      html += '</tr>';
      this.picker.find('.datetimepicker-days thead').append(html);
    },

    fillMonths: function () {
      var html = '';
      var d = new Date(this.viewDate);
      for (var i = 0; i < 12; i++) {
        d.setUTCMonth(i);
        var classes = this.onRenderMonth(d);
        html += '<span class="' + classes.join(' ') + '">' + dates[this.language].monthsShort[i] + '</span>';
      }
      this.picker.find('.datetimepicker-months td').html(html);
    },

    fill: function () {
      if (!this.date || !this.viewDate) {
        return;
      }
      var d = new Date(this.viewDate),
        year = d.getUTCFullYear(),
        month = d.getUTCMonth(),
        dayMonth = d.getUTCDate(),
        hours = d.getUTCHours(),
        startYear = this.startDate.getUTCFullYear(),
        startMonth = this.startDate.getUTCMonth(),
        endYear = this.endDate.getUTCFullYear(),
        endMonth = this.endDate.getUTCMonth() + 1,
        currentDate = (new UTCDate(this.date.getUTCFullYear(), this.date.getUTCMonth(), this.date.getUTCDate())).valueOf(),
        today = new Date();
      this.setTitle('.datetimepicker-days', dates[this.language].months[month] + ' ' + year)
      if (this.formatViewType === 'time') {
        var formatted = this.getFormattedDate();
        this.setTitle('.datetimepicker-hours', formatted);
        this.setTitle('.datetimepicker-minutes', formatted);
      } else {
        this.setTitle('.datetimepicker-hours', dayMonth + ' ' + dates[this.language].months[month] + ' ' + year);
        this.setTitle('.datetimepicker-minutes', dayMonth + ' ' + dates[this.language].months[month] + ' ' + year);
      }
      this.picker.find('tfoot th.today')
        .text(dates[this.language].today || dates['en'].today)
        .toggle(this.todayBtn !== false);
      this.picker.find('tfoot th.clear')
        .text(dates[this.language].clear || dates['en'].clear)
        .toggle(this.clearBtn !== false);
      this.updateNavArrows();
      this.fillMonths();
      var prevMonth = UTCDate(year, month - 1, 28, 0, 0, 0, 0),
        day = DPGlobal.getDaysInMonth(prevMonth.getUTCFullYear(), prevMonth.getUTCMonth());
      prevMonth.setUTCDate(day);
      prevMonth.setUTCDate(day - (prevMonth.getUTCDay() - this.weekStart + 7) % 7);
      var nextMonth = new Date(prevMonth);
      nextMonth.setUTCDate(nextMonth.getUTCDate() + 42);
      nextMonth = nextMonth.valueOf();
      var html = [];
      var classes;
      while (prevMonth.valueOf() < nextMonth) {
        if (prevMonth.getUTCDay() === this.weekStart) {
          html.push('<tr>');
        }
        classes = this.onRenderDay(prevMonth);
        if (prevMonth.getUTCFullYear() < year || (prevMonth.getUTCFullYear() === year && prevMonth.getUTCMonth() < month)) {
          classes.push('old');
        } else if (prevMonth.getUTCFullYear() > year || (prevMonth.getUTCFullYear() === year && prevMonth.getUTCMonth() > month)) {
          classes.push('new');
        }
        // Compare internal UTC date with local today, not UTC today
        if (this.todayHighlight &&
          prevMonth.getUTCFullYear() === today.getFullYear() &&
          prevMonth.getUTCMonth() === today.getMonth() &&
          prevMonth.getUTCDate() === today.getDate()) {
          classes.push('today');
        }
        if (prevMonth.valueOf() === currentDate) {
          classes.push('active');
        }
        if ((prevMonth.valueOf() + 86400000) <= this.startDate || prevMonth.valueOf() > this.endDate ||
          $.inArray(prevMonth.getUTCDay(), this.daysOfWeekDisabled) !== -1 ||
          $.inArray(prevMonth.toDateString(), this.datesDisabled) !== -1) {
          classes.push('disabled');
        }
        html.push('<td class="' + classes.join(' ') + '">' + prevMonth.getUTCDate() + '</td>');
        if (prevMonth.getUTCDay() === this.weekEnd) {
          html.push('</tr>');
        }
        prevMonth.setUTCDate(prevMonth.getUTCDate() + 1);
      }
      this.picker.find('.datetimepicker-days tbody').empty().append(html.join(''));

      html = [];
      var txt = '', meridian = '', meridianOld = '';
      var hoursDisabled = this.hoursDisabled || [];
      d = new Date(this.viewDate)
      for (var i = 0; i < 24; i++) {
        d.setUTCHours(i);
        classes = this.onRenderHour(d);
        if (hoursDisabled.indexOf(i) !== -1) {
          classes.push('disabled');
        }
        var actual = UTCDate(year, month, dayMonth, i);
        // We want the previous hour for the startDate
        if ((actual.valueOf() + 3600000) <= this.startDate || actual.valueOf() > this.endDate) {
          classes.push('disabled');
        } else if (hours === i) {
          classes.push('active');
        }
        if (this.showMeridian && dates[this.language].meridiem.length === 2) {
          meridian = (i < 12 ? dates[this.language].meridiem[0] : dates[this.language].meridiem[1]);
          if (meridian !== meridianOld) {
            if (meridianOld !== '') {
              html.push('</fieldset>');
            }
            html.push('<fieldset class="hour"><legend>' + meridian.toUpperCase() + '</legend>');
          }
          meridianOld = meridian;
          txt = (i % 12 ? i % 12 : 12);
          if (i < 12) {
            classes.push('hour_am');
          } else {
            classes.push('hour_pm');
          }
          html.push('<span class="' + classes.join(' ') + '">' + txt + '</span>');
          if (i === 23) {
            html.push('</fieldset>');
          }
        } else {
          txt = i + ':00';
          html.push('<span class="' + classes.join(' ') + '">' + txt + '</span>');
        }
      }
      this.picker.find('.datetimepicker-hours td').html(html.join(''));

      html = [];
      txt = '';
      meridian = '';
      meridianOld = '';
      var minutesDisabled = this.minutesDisabled || [];
      d = new Date(this.viewDate);
      for (var i = 0; i < 60; i += this.minuteStep) {
        if (minutesDisabled.indexOf(i) !== -1) continue;
        d.setUTCMinutes(i);
        d.setUTCSeconds(0);
        classes = this.onRenderMinute(d);
        if (this.showMeridian && dates[this.language].meridiem.length === 2) {
          meridian = (hours < 12 ? dates[this.language].meridiem[0] : dates[this.language].meridiem[1]);
          if (meridian !== meridianOld) {
            if (meridianOld !== '') {
              html.push('</fieldset>');
            }
            html.push('<fieldset class="minute"><legend>' + meridian.toUpperCase() + '</legend>');
          }
          meridianOld = meridian;
          txt = (hours % 12 ? hours % 12 : 12);
          html.push('<span class="' + classes.join(' ') + '">' + txt + ':' + (i < 10 ? '0' + i : i) + '</span>');
          if (i === 59) {
            html.push('</fieldset>');
          }
        } else {
          txt = i + ':00';
          html.push('<span class="' + classes.join(' ') + '">' + hours + ':' + (i < 10 ? '0' + i : i) + '</span>');
        }
      }
      this.picker.find('.datetimepicker-minutes td').html(html.join(''));

      var currentYear = this.date.getUTCFullYear();
      var months = this.setTitle('.datetimepicker-months', year)
        .end()
        .find('.month').removeClass('active');
      if (currentYear === year) {
        // getUTCMonths() returns 0 based, and we need to select the next one
        // To cater bootstrap 2 we don't need to select the next one
        months.eq(this.date.getUTCMonth()).addClass('active');
      }
      if (year < startYear || year > endYear) {
        months.addClass('disabled');
      }
      if (year === startYear) {
        months.slice(0, startMonth).addClass('disabled');
      }
      if (year === endYear) {
        months.slice(endMonth).addClass('disabled');
      }

      html = '';
      year = parseInt(year / 10, 10) * 10;
      var yearCont = this.setTitle('.datetimepicker-years', year + '-' + (year + 9))
        .end()
        .find('td');
      year -= 1;
      d = new Date(this.viewDate);
      for (var i = -1; i < 11; i++) {
        d.setUTCFullYear(year);
        classes = this.onRenderYear(d);
        if (i === -1 || i === 10) {
          classes.push(old);
        }
        html += '<span class="' + classes.join(' ') + '">' + year + '</span>';
        year += 1;
      }
      yearCont.html(html);
      this.place();
    },

    updateNavArrows: function () {
      var d = new Date(this.viewDate),
        year = d.getUTCFullYear(),
        month = d.getUTCMonth(),
        day = d.getUTCDate(),
        hour = d.getUTCHours();
      switch (this.viewMode) {
        case 0:
          if (year <= this.startDate.getUTCFullYear()
            && month <= this.startDate.getUTCMonth()
            && day <= this.startDate.getUTCDate()
            && hour <= this.startDate.getUTCHours()) {
            this.picker.find('.prev').css({visibility: 'hidden'});
          } else {
            this.picker.find('.prev').css({visibility: 'visible'});
          }
          if (year >= this.endDate.getUTCFullYear()
            && month >= this.endDate.getUTCMonth()
            && day >= this.endDate.getUTCDate()
            && hour >= this.endDate.getUTCHours()) {
            this.picker.find('.next').css({visibility: 'hidden'});
          } else {
            this.picker.find('.next').css({visibility: 'visible'});
          }
          break;
        case 1:
          if (year <= this.startDate.getUTCFullYear()
            && month <= this.startDate.getUTCMonth()
            && day <= this.startDate.getUTCDate()) {
            this.picker.find('.prev').css({visibility: 'hidden'});
          } else {
            this.picker.find('.prev').css({visibility: 'visible'});
          }
          if (year >= this.endDate.getUTCFullYear()
            && month >= this.endDate.getUTCMonth()
            && day >= this.endDate.getUTCDate()) {
            this.picker.find('.next').css({visibility: 'hidden'});
          } else {
            this.picker.find('.next').css({visibility: 'visible'});
          }
          break;
        case 2:
          if (year <= this.startDate.getUTCFullYear()
            && month <= this.startDate.getUTCMonth()) {
            this.picker.find('.prev').css({visibility: 'hidden'});
          } else {
            this.picker.find('.prev').css({visibility: 'visible'});
          }
          if (year >= this.endDate.getUTCFullYear()
            && month >= this.endDate.getUTCMonth()) {
            this.picker.find('.next').css({visibility: 'hidden'});
          } else {
            this.picker.find('.next').css({visibility: 'visible'});
          }
          break;
        case 3:
        case 4:
          if (year <= this.startDate.getUTCFullYear()) {
            this.picker.find('.prev').css({visibility: 'hidden'});
          } else {
            this.picker.find('.prev').css({visibility: 'visible'});
          }
          if (year >= this.endDate.getUTCFullYear()) {
            this.picker.find('.next').css({visibility: 'hidden'});
          } else {
            this.picker.find('.next').css({visibility: 'visible'});
          }
          break;
      }
    },

    mousewheel: function (e) {

      e.preventDefault();
      e.stopPropagation();

      if (this.wheelPause) {
        return;
      }

      this.wheelPause = true;

      var originalEvent = e.originalEvent;

      var delta = originalEvent.wheelDelta;

      var mode = delta > 0 ? 1 : (delta === 0) ? 0 : -1;

      if (this.wheelViewModeNavigationInverseDirection) {
        mode = -mode;
      }

      this.showMode(mode);

      setTimeout($.proxy(function () {

        this.wheelPause = false

      }, this), this.wheelViewModeNavigationDelay);

    },

    click: function (e) {
      e.stopPropagation();
      e.preventDefault();
      var target = $(e.target).closest('span, td, th, legend');
      if (target.is('.' + this.icontype)) {
        target = $(target).parent().closest('span, td, th, legend');
      }
      if (target.length === 1) {
        if (target.is('.disabled')) {
          this.element.trigger({
            type:      'outOfRange',
            date:      this.viewDate,
            startDate: this.startDate,
            endDate:   this.endDate
          });
          return;
        }
        switch (target[0].nodeName.toLowerCase()) {
          case 'th':
            switch (target[0].className) {
              case 'switch':
                this.showMode(1);
                break;
              case 'prev':
              case 'next':
                var dir = DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1);
                switch (this.viewMode) {
                  case 0:
                    this.viewDate = this.moveHour(this.viewDate, dir);
                    break;
                  case 1:
                    this.viewDate = this.moveDate(this.viewDate, dir);
                    break;
                  case 2:
                    this.viewDate = this.moveMonth(this.viewDate, dir);
                    break;
                  case 3:
                  case 4:
                    this.viewDate = this.moveYear(this.viewDate, dir);
                    break;
                }
                this.fill();
                this.element.trigger({
                  type:      target[0].className + ':' + this.convertViewModeText(this.viewMode),
                  date:      this.viewDate,
                  startDate: this.startDate,
                  endDate:   this.endDate
                });
                break;
              case 'clear':
                this.reset();
                if (this.autoclose) {
                  this.hide();
                }
                break;
              case 'today':
                var date = new Date();
                date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds(), 0);

                // Respect startDate and endDate.
                if (date < this.startDate) date = this.startDate;
                else if (date > this.endDate) date = this.endDate;

                this.viewMode = this.startViewMode;
                this.showMode(0);
                this._setDate(date);
                this.fill();
                if (this.autoclose) {
                  this.hide();
                }
                break;
            }
            break;
          case 'span':
            if (!target.is('.disabled')) {
              var year = this.viewDate.getUTCFullYear(),
                month = this.viewDate.getUTCMonth(),
                day = this.viewDate.getUTCDate(),
                hours = this.viewDate.getUTCHours(),
                minutes = this.viewDate.getUTCMinutes(),
                seconds = this.viewDate.getUTCSeconds();

              if (target.is('.month')) {
                this.viewDate.setUTCDate(1);
                month = target.parent().find('span').index(target);
                day = this.viewDate.getUTCDate();
                this.viewDate.setUTCMonth(month);
                this.element.trigger({
                  type: 'changeMonth',
                  date: this.viewDate
                });
                if (this.viewSelect >= 3) {
                  this._setDate(UTCDate(year, month, day, hours, minutes, seconds, 0));
                }
              } else if (target.is('.year')) {
                this.viewDate.setUTCDate(1);
                year = parseInt(target.text(), 10) || 0;
                this.viewDate.setUTCFullYear(year);
                this.element.trigger({
                  type: 'changeYear',
                  date: this.viewDate
                });
                if (this.viewSelect >= 4) {
                  this._setDate(UTCDate(year, month, day, hours, minutes, seconds, 0));
                }
              } else if (target.is('.hour')) {
                hours = parseInt(target.text(), 10) || 0;
                if (target.hasClass('hour_am') || target.hasClass('hour_pm')) {
                  if (hours === 12 && target.hasClass('hour_am')) {
                    hours = 0;
                  } else if (hours !== 12 && target.hasClass('hour_pm')) {
                    hours += 12;
                  }
                }
                this.viewDate.setUTCHours(hours);
                this.element.trigger({
                  type: 'changeHour',
                  date: this.viewDate
                });
                if (this.viewSelect >= 1) {
                  this._setDate(UTCDate(year, month, day, hours, minutes, seconds, 0));
                }
              } else if (target.is('.minute')) {
                minutes = parseInt(target.text().substr(target.text().indexOf(':') + 1), 10) || 0;
                this.viewDate.setUTCMinutes(minutes);
                this.element.trigger({
                  type: 'changeMinute',
                  date: this.viewDate
                });
                if (this.viewSelect >= 0) {
                  this._setDate(UTCDate(year, month, day, hours, minutes, seconds, 0));
                }
              }
              if (this.viewMode !== 0) {
                var oldViewMode = this.viewMode;
                this.showMode(-1);
                this.fill();
                if (oldViewMode === this.viewMode && this.autoclose) {
                  this.hide();
                }
              } else {
                this.fill();
                if (this.autoclose) {
                  this.hide();
                }
              }
            }
            break;
          case 'td':
            if (target.is('.day') && !target.is('.disabled')) {
              var day = parseInt(target.text(), 10) || 1;
              var year = this.viewDate.getUTCFullYear(),
                month = this.viewDate.getUTCMonth(),
                hours = this.viewDate.getUTCHours(),
                minutes = this.viewDate.getUTCMinutes(),
                seconds = this.viewDate.getUTCSeconds();
              if (target.is('.old')) {
                if (month === 0) {
                  month = 11;
                  year -= 1;
                } else {
                  month -= 1;
                }
              } else if (target.is('.new')) {
                if (month === 11) {
                  month = 0;
                  year += 1;
                } else {
                  month += 1;
                }
              }
              this.viewDate.setUTCFullYear(year);
              this.viewDate.setUTCMonth(month, day);
              this.element.trigger({
                type: 'changeDay',
                date: this.viewDate
              });
              if (this.viewSelect >= 2) {
                this._setDate(UTCDate(year, month, day, hours, minutes, seconds, 0));
              }
            }
            var oldViewMode = this.viewMode;
            this.showMode(-1);
            this.fill();
            if (oldViewMode === this.viewMode && this.autoclose) {
              this.hide();
            }
            break;
        }
      }
    },

    _setDate: function (date, which) {
      if (!which || which === 'date')
        this.date = date;
      if (!which || which === 'view')
        this.viewDate = date;
      this.fill();
      this.setValue();
      var element;
      if (this.isInput) {
        element = this.element;
      } else if (this.component) {
        element = this.element.find('input');
      }
      if (element) {
        element.change();
      }
      this.element.trigger({
        type: 'changeDate',
        date: this.getDate()
      });
      if(date === null)
        this.date = this.viewDate;
    },

    moveMinute: function (date, dir) {
      if (!dir) return date;
      var new_date = new Date(date.valueOf());
      //dir = dir > 0 ? 1 : -1;
      new_date.setUTCMinutes(new_date.getUTCMinutes() + (dir * this.minuteStep));
      return new_date;
    },

    moveHour: function (date, dir) {
      if (!dir) return date;
      var new_date = new Date(date.valueOf());
      //dir = dir > 0 ? 1 : -1;
      new_date.setUTCHours(new_date.getUTCHours() + dir);
      return new_date;
    },

    moveDate: function (date, dir) {
      if (!dir) return date;
      var new_date = new Date(date.valueOf());
      //dir = dir > 0 ? 1 : -1;
      new_date.setUTCDate(new_date.getUTCDate() + dir);
      return new_date;
    },

    moveMonth: function (date, dir) {
      if (!dir) return date;
      var new_date = new Date(date.valueOf()),
        day = new_date.getUTCDate(),
        month = new_date.getUTCMonth(),
        mag = Math.abs(dir),
        new_month, test;
      dir = dir > 0 ? 1 : -1;
      if (mag === 1) {
        test = dir === -1
          // If going back one month, make sure month is not current month
          // (eg, Mar 31 -> Feb 31 === Feb 28, not Mar 02)
          ? function () {
          return new_date.getUTCMonth() === month;
        }
          // If going forward one month, make sure month is as expected
          // (eg, Jan 31 -> Feb 31 === Feb 28, not Mar 02)
          : function () {
          return new_date.getUTCMonth() !== new_month;
        };
        new_month = month + dir;
        new_date.setUTCMonth(new_month);
        // Dec -> Jan (12) or Jan -> Dec (-1) -- limit expected date to 0-11
        if (new_month < 0 || new_month > 11)
          new_month = (new_month + 12) % 12;
      } else {
        // For magnitudes >1, move one month at a time...
        for (var i = 0; i < mag; i++)
          // ...which might decrease the day (eg, Jan 31 to Feb 28, etc)...
          new_date = this.moveMonth(new_date, dir);
        // ...then reset the day, keeping it in the new month
        new_month = new_date.getUTCMonth();
        new_date.setUTCDate(day);
        test = function () {
          return new_month !== new_date.getUTCMonth();
        };
      }
      // Common date-resetting loop -- if date is beyond end of month, make it
      // end of month
      while (test()) {
        new_date.setUTCDate(--day);
        new_date.setUTCMonth(new_month);
      }
      return new_date;
    },

    moveYear: function (date, dir) {
      return this.moveMonth(date, dir * 12);
    },

    dateWithinRange: function (date) {
      return date >= this.startDate && date <= this.endDate;
    },

    keydown: function (e) {
      if (this.picker.is(':not(:visible)')) {
        if (e.keyCode === 27) // allow escape to hide and re-show picker
          this.show();
        return;
      }
      var dateChanged = false,
        dir, newDate, newViewDate;
      switch (e.keyCode) {
        case 27: // escape
          this.hide();
          e.preventDefault();
          break;
        case 37: // left
        case 39: // right
          if (!this.keyboardNavigation) break;
          dir = e.keyCode === 37 ? -1 : 1;
          var viewMode = this.viewMode;
          if (e.ctrlKey) {
            viewMode += 2;
          } else if (e.shiftKey) {
            viewMode += 1;
          }
          if (viewMode === 4) {
            newDate = this.moveYear(this.date, dir);
            newViewDate = this.moveYear(this.viewDate, dir);
          } else if (viewMode === 3) {
            newDate = this.moveMonth(this.date, dir);
            newViewDate = this.moveMonth(this.viewDate, dir);
          } else if (viewMode === 2) {
            newDate = this.moveDate(this.date, dir);
            newViewDate = this.moveDate(this.viewDate, dir);
          } else if (viewMode === 1) {
            newDate = this.moveHour(this.date, dir);
            newViewDate = this.moveHour(this.viewDate, dir);
          } else if (viewMode === 0) {
            newDate = this.moveMinute(this.date, dir);
            newViewDate = this.moveMinute(this.viewDate, dir);
          }
          if (this.dateWithinRange(newDate)) {
            this.date = newDate;
            this.viewDate = newViewDate;
            this.setValue();
            this.update();
            e.preventDefault();
            dateChanged = true;
          }
          break;
        case 38: // up
        case 40: // down
          if (!this.keyboardNavigation) break;
          dir = e.keyCode === 38 ? -1 : 1;
          viewMode = this.viewMode;
          if (e.ctrlKey) {
            viewMode += 2;
          } else if (e.shiftKey) {
            viewMode += 1;
          }
          if (viewMode === 4) {
            newDate = this.moveYear(this.date, dir);
            newViewDate = this.moveYear(this.viewDate, dir);
          } else if (viewMode === 3) {
            newDate = this.moveMonth(this.date, dir);
            newViewDate = this.moveMonth(this.viewDate, dir);
          } else if (viewMode === 2) {
            newDate = this.moveDate(this.date, dir * 7);
            newViewDate = this.moveDate(this.viewDate, dir * 7);
          } else if (viewMode === 1) {
            if (this.showMeridian) {
              newDate = this.moveHour(this.date, dir * 6);
              newViewDate = this.moveHour(this.viewDate, dir * 6);
            } else {
              newDate = this.moveHour(this.date, dir * 4);
              newViewDate = this.moveHour(this.viewDate, dir * 4);
            }
          } else if (viewMode === 0) {
            newDate = this.moveMinute(this.date, dir * 4);
            newViewDate = this.moveMinute(this.viewDate, dir * 4);
          }
          if (this.dateWithinRange(newDate)) {
            this.date = newDate;
            this.viewDate = newViewDate;
            this.setValue();
            this.update();
            e.preventDefault();
            dateChanged = true;
          }
          break;
        case 13: // enter
          if (this.viewMode !== 0) {
            var oldViewMode = this.viewMode;
            this.showMode(-1);
            this.fill();
            if (oldViewMode === this.viewMode && this.autoclose) {
              this.hide();
            }
          } else {
            this.fill();
            if (this.autoclose) {
              this.hide();
            }
          }
          e.preventDefault();
          break;
        case 9: // tab
          this.hide();
          break;
      }
      if (dateChanged) {
        var element;
        if (this.isInput) {
          element = this.element;
        } else if (this.component) {
          element = this.element.find('input');
        }
        if (element) {
          element.change();
        }
        this.element.trigger({
          type: 'changeDate',
          date: this.getDate()
        });
      }
    },

    showMode: function (dir) {
      if (dir) {
        var newViewMode = Math.max(0, Math.min(DPGlobal.modes.length - 1, this.viewMode + dir));
        if (newViewMode >= this.minView && newViewMode <= this.maxView) {
          this.element.trigger({
            type:        'changeMode',
            date:        this.viewDate,
            oldViewMode: this.viewMode,
            newViewMode: newViewMode
          });

          this.viewMode = newViewMode;
        }
      }
      /*
       vitalets: fixing bug of very special conditions:
       jquery 1.7.1 + webkit + show inline datetimepicker in bootstrap popover.
       Method show() does not set display css correctly and datetimepicker is not shown.
       Changed to .css('display', 'block') solve the problem.
       See https://github.com/vitalets/x-editable/issues/37

       In jquery 1.7.2+ everything works fine.
       */
      //this.picker.find('>div').hide().filter('.datetimepicker-'+DPGlobal.modes[this.viewMode].clsName).show();
      this.picker.find('>div').hide().filter('.datetimepicker-' + DPGlobal.modes[this.viewMode].clsName).css('display', 'block');
      this.updateNavArrows();
    },

    reset: function () {
      this._setDate(null, 'date');
    },

    convertViewModeText:  function (viewMode) {
      switch (viewMode) {
        case 4:
          return 'decade';
        case 3:
          return 'year';
        case 2:
          return 'month';
        case 1:
          return 'day';
        case 0:
          return 'hour';
      }
    }
  };

  var old = $.fn.datetimepicker;
  $.fn.datetimepicker = function (option) {
    var args = Array.apply(null, arguments);
    args.shift();
    var internal_return;
    this.each(function () {
      var $this = $(this),
        data = $this.data('datetimepicker'),
        options = typeof option === 'object' && option;
      if (!data) {
        $this.data('datetimepicker', (data = new Datetimepicker(this, $.extend({}, $.fn.datetimepicker.defaults, options))));
      }
      if (typeof option === 'string' && typeof data[option] === 'function') {
        internal_return = data[option].apply(data, args);
        if (internal_return !== undefined) {
          return false;
        }
      }
    });
    if (internal_return !== undefined)
      return internal_return;
    else
      return this;
  };

  $.fn.datetimepicker.defaults = {
  };
  $.fn.datetimepicker.Constructor = Datetimepicker;
  var dates = $.fn.datetimepicker.dates = {
    en: {
      days:        ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
      daysShort:   ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
      daysMin:     ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
      months:      ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
      monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      meridiem:    ['am', 'pm'],
      suffix:      ['st', 'nd', 'rd', 'th'],
      today:       'Today',
      clear:       'Clear'
    }
  };

  var DPGlobal = {
    modes:            [
      {
        clsName: 'minutes',
        navFnc:  'Hours',
        navStep: 1
      },
      {
        clsName: 'hours',
        navFnc:  'Date',
        navStep: 1
      },
      {
        clsName: 'days',
        navFnc:  'Month',
        navStep: 1
      },
      {
        clsName: 'months',
        navFnc:  'FullYear',
        navStep: 1
      },
      {
        clsName: 'years',
        navFnc:  'FullYear',
        navStep: 10
      }
    ],
    isLeapYear:       function (year) {
      return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0))
    },
    getDaysInMonth:   function (year, month) {
      return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month]
    },
    getDefaultFormat: function (type, field) {
      if (type === 'standard') {
        if (field === 'input')
          return 'yyyy-mm-dd hh:ii';
        else
          return 'yyyy-mm-dd hh:ii:ss';
      } else if (type === 'php') {
        if (field === 'input')
          return 'Y-m-d H:i';
        else
          return 'Y-m-d H:i:s';
      } else {
        throw new Error('Invalid format type.');
      }
    },
    validParts: function (type) {
      if (type === 'standard') {
        return /t|hh?|HH?|p|P|z|Z|ii?|ss?|dd?|DD?|mm?|MM?|yy(?:yy)?/g;
      } else if (type === 'php') {
        return /[dDjlNwzFmMnStyYaABgGhHis]/g;
      } else {
        throw new Error('Invalid format type.');
      }
    },
    nonpunctuation: /[^ -\/:-@\[-`{-~\t\n\rTZ]+/g,
    parseFormat: function (format, type) {
      // IE treats \0 as a string end in inputs (truncating the value),
      // so it's a bad format delimiter, anyway
      var separators = format.replace(this.validParts(type), '\0').split('\0'),
        parts = format.match(this.validParts(type));
      if (!separators || !separators.length || !parts || parts.length === 0) {
        throw new Error('Invalid date format.');
      }
      return {separators: separators, parts: parts};
    },
    parseDate: function (date, format, language, type, timezone) {
      if (date instanceof Date) {
        var dateUTC = new Date(date.valueOf() - date.getTimezoneOffset() * 60000);
        dateUTC.setMilliseconds(0);
        return dateUTC;
      }
      if (/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(date)) {
        format = this.parseFormat('yyyy-mm-dd', type);
      }
      if (/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}$/.test(date)) {
        format = this.parseFormat('yyyy-mm-dd hh:ii', type);
      }
      if (/^\d{4}\-\d{1,2}\-\d{1,2}[T ]\d{1,2}\:\d{1,2}\:\d{1,2}[Z]{0,1}$/.test(date)) {
        format = this.parseFormat('yyyy-mm-dd hh:ii:ss', type);
      }
      if (/^[-+]\d+[dmwy]([\s,]+[-+]\d+[dmwy])*$/.test(date)) {
        var part_re = /([-+]\d+)([dmwy])/,
          parts = date.match(/([-+]\d+)([dmwy])/g),
          part, dir;
        date = new Date();
        for (var i = 0; i < parts.length; i++) {
          part = part_re.exec(parts[i]);
          dir = parseInt(part[1]);
          switch (part[2]) {
            case 'd':
              date.setUTCDate(date.getUTCDate() + dir);
              break;
            case 'm':
              date = Datetimepicker.prototype.moveMonth.call(Datetimepicker.prototype, date, dir);
              break;
            case 'w':
              date.setUTCDate(date.getUTCDate() + dir * 7);
              break;
            case 'y':
              date = Datetimepicker.prototype.moveYear.call(Datetimepicker.prototype, date, dir);
              break;
          }
        }
        return UTCDate(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds(), 0);
      }
      var parts = date && date.toString().match(this.nonpunctuation) || [],
        date = new Date(0, 0, 0, 0, 0, 0, 0),
        parsed = {},
        setters_order = ['hh', 'h', 'ii', 'i', 'ss', 's', 'yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'D', 'DD', 'd', 'dd', 'H', 'HH', 'p', 'P', 'z', 'Z'],
        setters_map = {
          hh:   function (d, v) {
            return d.setUTCHours(v);
          },
          h:    function (d, v) {
            return d.setUTCHours(v);
          },
          HH:   function (d, v) {
            return d.setUTCHours(v === 12 ? 0 : v);
          },
          H:    function (d, v) {
            return d.setUTCHours(v === 12 ? 0 : v);
          },
          ii:   function (d, v) {
            return d.setUTCMinutes(v);
          },
          i:    function (d, v) {
            return d.setUTCMinutes(v);
          },
          ss:   function (d, v) {
            return d.setUTCSeconds(v);
          },
          s:    function (d, v) {
            return d.setUTCSeconds(v);
          },
          yyyy: function (d, v) {
            return d.setUTCFullYear(v);
          },
          yy:   function (d, v) {
            return d.setUTCFullYear(2000 + v);
          },
          m:    function (d, v) {
            v -= 1;
            while (v < 0) v += 12;
            v %= 12;
            d.setUTCMonth(v);
            while (d.getUTCMonth() !== v)
              if (isNaN(d.getUTCMonth()))
                return d;
              else
                d.setUTCDate(d.getUTCDate() - 1);
            return d;
          },
          d:    function (d, v) {
            return d.setUTCDate(v);
          },
          p:    function (d, v) {
            return d.setUTCHours(v === 1 ? d.getUTCHours() + 12 : d.getUTCHours());
          },
          z:    function () {
            return timezone
          }
        },
        val, filtered, part;
      setters_map['M'] = setters_map['MM'] = setters_map['mm'] = setters_map['m'];
      setters_map['dd'] = setters_map['d'];
      setters_map['P'] = setters_map['p'];
      setters_map['Z'] = setters_map['z'];
      date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), date.getHours(), date.getMinutes(), date.getSeconds());
      if (parts.length === format.parts.length) {
        for (var i = 0, cnt = format.parts.length; i < cnt; i++) {
          val = parseInt(parts[i], 10);
          part = format.parts[i];
          if (isNaN(val)) {
            switch (part) {
              case 'MM':
                filtered = $(dates[language].months).filter(function () {
                  var m = this.slice(0, parts[i].length),
                    p = parts[i].slice(0, m.length);
                  return m === p;
                });
                val = $.inArray(filtered[0], dates[language].months) + 1;
                break;
              case 'M':
                filtered = $(dates[language].monthsShort).filter(function () {
                  var m = this.slice(0, parts[i].length),
                    p = parts[i].slice(0, m.length);
                  return m.toLowerCase() === p.toLowerCase();
                });
                val = $.inArray(filtered[0], dates[language].monthsShort) + 1;
                break;
              case 'p':
              case 'P':
                val = $.inArray(parts[i].toLowerCase(), dates[language].meridiem);
                break;
              case 'z':
              case 'Z':
                timezone;
                break;

            }
          }
          parsed[part] = val;
        }
        for (var i = 0, s; i < setters_order.length; i++) {
          s = setters_order[i];
          if (s in parsed && !isNaN(parsed[s]))
            setters_map[s](date, parsed[s])
        }
      }
      return date;
    },
    formatDate:       function (date, format, language, type, timezone) {
      if (date === null) {
        return '';
      }
      var val;
      if (type === 'standard') {
        val = {
          t:    date.getTime(),
          // year
          yy:   date.getUTCFullYear().toString().substring(2),
          yyyy: date.getUTCFullYear(),
          // month
          m:    date.getUTCMonth() + 1,
          M:    dates[language].monthsShort[date.getUTCMonth()],
          MM:   dates[language].months[date.getUTCMonth()],
          // day
          d:    date.getUTCDate(),
          D:    dates[language].daysShort[date.getUTCDay()],
          DD:   dates[language].days[date.getUTCDay()],
          p:    (dates[language].meridiem.length === 2 ? dates[language].meridiem[date.getUTCHours() < 12 ? 0 : 1] : ''),
          // hour
          h:    date.getUTCHours(),
          // minute
          i:    date.getUTCMinutes(),
          // second
          s:    date.getUTCSeconds(),
          // timezone
          z:    timezone
        };

        if (dates[language].meridiem.length === 2) {
          val.H = (val.h % 12 === 0 ? 12 : val.h % 12);
        }
        else {
          val.H = val.h;
        }
        val.HH = (val.H < 10 ? '0' : '') + val.H;
        val.P = val.p.toUpperCase();
        val.Z = val.z;
        val.hh = (val.h < 10 ? '0' : '') + val.h;
        val.ii = (val.i < 10 ? '0' : '') + val.i;
        val.ss = (val.s < 10 ? '0' : '') + val.s;
        val.dd = (val.d < 10 ? '0' : '') + val.d;
        val.mm = (val.m < 10 ? '0' : '') + val.m;
      } else if (type === 'php') {
        // php format
        val = {
          // year
          y: date.getUTCFullYear().toString().substring(2),
          Y: date.getUTCFullYear(),
          // month
          F: dates[language].months[date.getUTCMonth()],
          M: dates[language].monthsShort[date.getUTCMonth()],
          n: date.getUTCMonth() + 1,
          t: DPGlobal.getDaysInMonth(date.getUTCFullYear(), date.getUTCMonth()),
          // day
          j: date.getUTCDate(),
          l: dates[language].days[date.getUTCDay()],
          D: dates[language].daysShort[date.getUTCDay()],
          w: date.getUTCDay(), // 0 -> 6
          N: (date.getUTCDay() === 0 ? 7 : date.getUTCDay()),       // 1 -> 7
          S: (date.getUTCDate() % 10 <= dates[language].suffix.length ? dates[language].suffix[date.getUTCDate() % 10 - 1] : ''),
          // hour
          a: (dates[language].meridiem.length === 2 ? dates[language].meridiem[date.getUTCHours() < 12 ? 0 : 1] : ''),
          g: (date.getUTCHours() % 12 === 0 ? 12 : date.getUTCHours() % 12),
          G: date.getUTCHours(),
          // minute
          i: date.getUTCMinutes(),
          // second
          s: date.getUTCSeconds()
        };
        val.m = (val.n < 10 ? '0' : '') + val.n;
        val.d = (val.j < 10 ? '0' : '') + val.j;
        val.A = val.a.toString().toUpperCase();
        val.h = (val.g < 10 ? '0' : '') + val.g;
        val.H = (val.G < 10 ? '0' : '') + val.G;
        val.i = (val.i < 10 ? '0' : '') + val.i;
        val.s = (val.s < 10 ? '0' : '') + val.s;
      } else {
        throw new Error('Invalid format type.');
      }
      var date = [],
        seps = $.extend([], format.separators);
      for (var i = 0, cnt = format.parts.length; i < cnt; i++) {
        if (seps.length) {
          date.push(seps.shift());
        }
        date.push(val[format.parts[i]]);
      }
      if (seps.length) {
        date.push(seps.shift());
      }
      return date.join('');
    },
    convertViewMode:  function (viewMode) {
      switch (viewMode) {
        case 4:
        case 'decade':
          viewMode = 4;
          break;
        case 3:
        case 'year':
          viewMode = 3;
          break;
        case 2:
        case 'month':
          viewMode = 2;
          break;
        case 1:
        case 'day':
          viewMode = 1;
          break;
        case 0:
        case 'hour':
          viewMode = 0;
          break;
      }

      return viewMode;
    },
    headTemplate: '<thead>' +
                '<tr>' +
                '<th class="prev"><i class="{iconType} {leftArrow}"/></th>' +
                '<th colspan="5" class="switch"></th>' +
                '<th class="next"><i class="{iconType} {rightArrow}"/></th>' +
                '</tr>' +
      '</thead>',
    headTemplateV3: '<thead>' +
                '<tr>' +
                '<th class="prev"><span class="{iconType} {leftArrow}"></span> </th>' +
                '<th colspan="5" class="switch"></th>' +
                '<th class="next"><span class="{iconType} {rightArrow}"></span> </th>' +
                '</tr>' +
      '</thead>',
    contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
    footTemplate: '<tfoot>' + 
                    '<tr><th colspan="7" class="today"></th></tr>' +
                    '<tr><th colspan="7" class="clear"></th></tr>' +
                  '</tfoot>'
  };
  DPGlobal.template = '<div class="datetimepicker">' +
    '<div class="datetimepicker-minutes">' +
    '<table class=" table-condensed">' +
    DPGlobal.headTemplate +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-hours">' +
    '<table class=" table-condensed">' +
    DPGlobal.headTemplate +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-days">' +
    '<table class=" table-condensed">' +
    DPGlobal.headTemplate +
    '<tbody></tbody>' +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-months">' +
    '<table class="table-condensed">' +
    DPGlobal.headTemplate +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-years">' +
    '<table class="table-condensed">' +
    DPGlobal.headTemplate +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '</div>';
  DPGlobal.templateV3 = '<div class="datetimepicker">' +
    '<div class="datetimepicker-minutes">' +
    '<table class=" table-condensed">' +
    DPGlobal.headTemplateV3 +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-hours">' +
    '<table class=" table-condensed">' +
    DPGlobal.headTemplateV3 +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-days">' +
    '<table class=" table-condensed">' +
    DPGlobal.headTemplateV3 +
    '<tbody></tbody>' +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-months">' +
    '<table class="table-condensed">' +
    DPGlobal.headTemplateV3 +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '<div class="datetimepicker-years">' +
    '<table class="table-condensed">' +
    DPGlobal.headTemplateV3 +
    DPGlobal.contTemplate +
    DPGlobal.footTemplate +
    '</table>' +
    '</div>' +
    '</div>';
  $.fn.datetimepicker.DPGlobal = DPGlobal;

  /* DATETIMEPICKER NO CONFLICT
   * =================== */

  $.fn.datetimepicker.noConflict = function () {
    $.fn.datetimepicker = old;
    return this;
  };

  /* DATETIMEPICKER DATA-API
   * ================== */

  $(document).on(
    'focus.datetimepicker.data-api click.datetimepicker.data-api',
    '[data-provide="datetimepicker"]',
    function (e) {
      var $this = $(this);
      if ($this.data('datetimepicker')) return;
      e.preventDefault();
      // component click requires us to explicitly show it
      $this.datetimepicker('show');
    }
  );
  $(function () {
    $('[data-provide="datetimepicker-inline"]').datetimepicker();
  });

}));
