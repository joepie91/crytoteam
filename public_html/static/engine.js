/* Prototype modifications of standard types go here. */

HTMLCanvasElement.prototype.relativeCoordinates = function(event)
{
	var totalOffsetX = 0;
	var totalOffsetY = 0;
	var canvasX = 0;
	var canvasY = 0;
	var currentElement = this;
	
	do {
		if(!$(currentElement).is('body'))
		{
			totalOffsetX += currentElement.offsetLeft - currentElement.scrollLeft;
			totalOffsetY += currentElement.offsetTop - currentElement.scrollTop;
		}
	} while(currentElement = currentElement.offsetParent)

	canvasX = event.pageX - totalOffsetX;
	canvasY = event.pageY - totalOffsetY;

	return {x:canvasX, y:canvasY}
}

/* Core engine constructor. */
function Engine(settings)
{
	this.resources = {};
	this.sprites = {};
	this.sounds = {};
	this.objects = {};
	this.scenes = {};
	this.canvas = null;
	this.settings = settings;
	this.loader_created = false;
	this.instance_increment = 10000;
	
	_engine = this;

	/**--------------------------------------**/
	/**              PRELOADER               **/
	/**--------------------------------------**/
	
	this.Preloader = function()
	{
		this._engine = _engine;
		this.queue = [];
		this.resources_left = 0;
		this.resources_done = 0;
		this.resources_total = 0;
		this.resources_failed = 0;
		this.images_total = 0;
		this.scripts_total = 0;
		this.sounds_total = 0;
		this.sound_enabled = false;
		this.sound_ignore_failure = false;
		
		if(this._engine.loader_created == false)
		{
			/* This is the first Preloader that is being created. Load scripts that are needed by the engine. */
			this._engine.loader_created = true;
			
			if(typeof this._engine.settings !== "undefined")
			{
				if(this._engine.settings["enable_sound"])
				{
					this.AddScript("static/soundmanager2.js", "SoundManager2");
					
					this.AddFunction(function(){
						soundManager.setup({
							url: 'static/',
							onready: this._HandleSM2Ready.bind(this),
							ontimeout: this._HandleSM2Failed.bind(this),
							useHighPerformance: true,
							preferFlash: false
						});
						
						return false;
					}, "Initializing SoundManager2");
				}
				else
				{
					this._engine.sound_enabled = false;
				}
			}
		}
	}
	
	/* Add function */
	this.Preloader.prototype.AddFunction = function(func, description)
	{
		this.queue.push({
			type: "function",
			func: func.bind(this),
			desc: description
		});
		
		this.resources_left++;
		this.resources_total++;
	}
	
	/* Add image */
	this.Preloader.prototype.AddImage = function(name, path)
	{
		this.queue.push({
			type: "image",
			name: name,
			path: path
		});
		
		this.resources_left++;
		this.resources_total++;
	}
	
	/* Add a dictionary of items */
	this.Preloader.prototype.AddItems = function(items)
	{
		if(typeof items.images != "undefined")
		{
			for(name in items.images)
			{
				this.AddImage(name, items.images[name]);
			}
		}
		
		if(typeof items.sounds != "undefined")
		{
			for(name in items.sounds)
			{
				this.AddSound(name, items.sounds[name]);
			}
		}
		
		if(typeof items.scripts != "undefined")
		{
			for(name in items.scripts)
			{
				this.AddScript(name, items.scripts[name]);
			}
		}
		
		if(typeof items.functions != "undefined")
		{
			for(name in items.functions)
			{
				this.AddFunction(name, items.functions[name]);
			}
		}
		
	}
	
	/* Add script file */
	this.Preloader.prototype.AddScript = function(path, description)
	{
		this.queue.push({
			type: "script",
			path: path,
			desc: description
		});
		
		this.resources_left++;
		this.resources_total++;
	}
	
	/* Add sound file */
	this.Preloader.prototype.AddSound = function(name, path)
	{
		this.queue.push({
			type: "sound",
			name: name,
			path: path
		});
		
		this.resources_left++;
		this.resources_total++;
	}
	
	/* Start the preloading sequence. */
	this.Preloader.prototype.Run = function(callbacks)
	{
		if(typeof callbacks == "undefined")
		{
			this.callbacks = {};
		}
		else
		{
			this.callbacks = callbacks;
		}
		
		this.RunCycle();
	}
	
	/* Process one item in the preloading sequence. */
	this.Preloader.prototype.RunCycle = function()
	{
		if(this.queue.length > 0)
		{
			current_object = this.queue.shift();
			
			if(current_object.type == "image")
			{
				this._DoCallback(this.callbacks.onstagechange, {
					type: "image",
					description: current_object.path
				});
				
				current_image = new Image();
				current_image.onload = this._ReportResourceFinished.bind(this);
				current_image.src = current_object.path;
				
				this.images_total++;
				this._engine.resources[current_object.name] = current_image;
			}
			else if(current_object.type == "sound")
			{
				this._DoCallback(this.callbacks.onstagechange, {
					type: "sound",
					description: current_object.path
				});
				
				if(this._engine.sound_enabled == true)
				{
					if(soundManager.canPlayURL(current_object.path))
					{
						var sound = soundManager.createSound({
							id: current_object.name,
							url: current_object.path,
							autoLoad: true,
							autoPlay: false,
							onload: this._ReportResourceFinished.bind(this)
						});
						
						this.sounds_total++;
						this._engine.resources[current_object.name] = sound;
					}
					else
					{
						throw {
							"name": "SoundError",
							"message": "The sound format is not supported."
						}
					}
				}
				else if(this._engine.sound_ignore_failure == true)
				{
					/* TODO: Log error */
				}
				else
				{
					throw {
						"name": "SoundError",
						"message": "Cannot add audio file because sound support is not enabled in the engine."
					}
				}
			}
			else if(current_object.type == "script")
			{
				this._DoCallback(this.callbacks.onstagechange, {
					type: "script",
					description: current_object.desc
				});
				
				$.getScript(current_object.path, this._ReportResourceFinished.bind(this));
				
				this.scripts_total++;
			}
			else if(current_object.type == "function")
			{
				this._DoCallback(this.callbacks.onstagechange, {
					type: "function",
					description: current_object.desc
				});
				
				var result = current_object.func(this.callbacks);
				
				if(result == true)
				{
					this._ReportResourceFinished();
				}
			}
		}
		else
		{
			this._DoCallback(this.callbacks.onfinish)
		}
	}
	
	/* Check if the preloading sequence is finished and take appropriate action. */
	this.Preloader.prototype._CheckIfDone = function()
	{
		if(this.resources_left <= 0)
		{
			this._DoCallback(this.callbacks.onfinish);
		}
		else
		{
			this.RunCycle();
		}
	}
	
	/* Do a pre-specified callback if it exists. */
	this.Preloader.prototype._DoCallback = function(callback, data)
	{
		if(typeof callback != "undefined")
		{
			callback(data);
		}
	}
	
	/* Handle successful finishing of the SoundManager2 loading process. */
	this.Preloader.prototype._HandleSM2Ready = function()
	{
		this._engine.sound_enabled = true;
		this._ReportResourceFinished();
	}
	
	/* Handle failure of the SoundManager2 loading process. */
	this.Preloader.prototype._HandleSM2Failed = function()
	{
		this._engine.sound_enabled = false;
		this._ReportResourceFinished();
	}
	
	/* Mark the currently preloading resource as finished. */
	this.Preloader.prototype._ReportResourceFinished = function()
	{
		this.resources_left--;
		this.resources_done++;
		
		this._DoCallback(this.callbacks.onprogress, {
			done: this.resources_done,
			total: this.resources_total,
			left: this.resources_left,
			failures: this.resources_failed
		});
		
		this._CheckIfDone();
	}
	
	/* Mark the currently preloading resource as failed. */
	this.Preloader.prototype._ReportResourceFailed = function()
	{
		/* TODO: Implement. */
		this.resources_left--;
		this.resources_failed++;
	}
	
	
	/**--------------------------------------**/
	/**     BASE PROPERTIES AND METHODS      **/
	/**--------------------------------------**/
	
	this.Base = {
		timers: {},
		SetTimer: function(duration, func)
		{
			setTimeout((function(self, f){ return f.call.bind(f, self); })(this, func), duration);
		},
		SetInterval: function(name, interval, func)
		{
			this.timers[name] = setInterval((function(self, f){ return f.call.bind(f, self); })(this, func), interval);
		},
		CancelInterval: function(name)
		{
			clearInterval(this.timers[name]);
		},
		CallEvent: function(func, data)
		{
			if(typeof func !== "undefined")
			{
				return func.call(this, data);
			}
		},
		_BubbleEvent: function(func, eventdata, instances)
		{
			list = instances.slice().reverse();
			
			var hit_event = false;
			
			for(item in list)
			{
				var object = list[item];
				var result = object.CallEvent(object[func], eventdata);
				
				if(typeof result != "undefined")
				{
					if(result == false)
					{
						break;
					}
					else if(result == true)
					{
						hit_event = true;
					}
				}
			}
			
			return hit_event;
		}
	}
	
	
	/**--------------------------------------**/
	/**                SCENES                **/
	/**--------------------------------------**/
	
	this.Scene = function(name, options)
	{
		if(typeof _engine.scenes[name] !== "undefined")
		{
			throw {
				name: "NameError",
				message: "A scene with the given name already exists."
			}
		}
		
		this._engine = _engine;
		this.canvas = null;
		this.instances = [];
		this.dirty = true;
		this.width = 640;
		this.height = 480;
		this.fps = 45;
		this.name = name;
		this.cached_selectors = {};
		this.mouse_coordinates = {x: 0, y: 0};
		this.step_counter = 0;
		this.draw_counter = 0;
		this.current_fps = 0;
		this.date = new Date;
		
		$.extend(true, this, options, this._engine.Base);
		
		this._engine.scenes[name] = this;
	}
	
	this.Scene.prototype.Add = function(object)
	{	
		if(typeof object == "string")
		{
			if(typeof this._engine.objects[object] !== "undefined")
			{
				var instance = this._engine.CreateInstance(object);
			}
			else
			{
				throw {
					"name": "ObjectError",
					"message": "The specified object does not exist."
				}
			}
		}
		else
		{
			var instance = object;
		}
		
		instance.scene = this;
		this.instances.push(instance);
		this.dirty = true;
	}
	
	this.Scene.prototype.Attach = function(canvas)
	{
		if(typeof $(canvas).data("current-scene") !== "undefined")
		{
			/* A different scene was previously attached to this canvas.
			 * Let's detach it first. */
			var previous_scene = $(canvas).data("current-scene");
			this._engine.scenes[previous_scene].Detach();
		}
		
		this.canvas = canvas;
		this._engine.canvas = canvas;
		canvas.width = this.width;
		canvas.height = this.height;
		$(canvas).data("current-scene", this.name);
		
		$(canvas).click(this._ProcessClickEvent.bind(this));
		$(canvas).mousemove(this._ProcessMouseMoveEvent.bind(this));
		
		this.CallEvent(this.OnLoad);
		this._Initialize();
	}
	
	this.Scene.prototype.Detach = function()
	{
		$(this.canvas).unbind('click');
		$(this.canvas).unbind('mousemove');
		$(this.canvas).removeData("current-scene");
		this.canvas = null;
	}
	
	this.Scene.prototype.DrawText = function(text, options)
	{
		var type = "fill", color = "#000000", x = 0, y = 0;
			
		if(typeof options.outline != "undefined" && options.outline)
		{
			type = "outline";
		}
		
		if(typeof options.color != "undefined")
		{
			color = options.color;
		}
		
		if(typeof options.x != "undefined")
		{
			x = options.x;
		}
		
		if(typeof options.y != "undefined")
		{
			y = options.y;
		}
		
		ctx = this._GetTextContext(options);
		
		if(type == "fill")
		{
			ctx.fillStyle = color;
			ctx.fillText(text, x, y);
		}
		else if(type == "outline")
		{
			ctx.strokeStyle = color;
			ctx.outlineText(text, x, y);
		}
		
		ctx.restore();
	}
	
	this.Scene.prototype.DrawTextCentered = function(text, options)
	{
		var x = 0, scale = 1, width = this.GetTextWidth(text, options);
			
		if(typeof options.x != "undefined")
		{
			x = options.x;
		}
		
		if(typeof options.scale != "undefined")
		{
			scale = options.scale;
		}
		
		x = x - ((width / 2) * scale * scale);
		
		options.x = x;
		
		this.DrawText(text, options);
	}
	
	this.Scene.prototype.GetTextWidth = function(text, options)
	{
		ctx = this._GetTextContext(options);
		var width = ctx.measureText(text).width;
		ctx.restore();
		
		return width;
	}
	
	this.Scene.prototype.Redraw = function()
	{
		this.dirty = true;
	}
	
	this.Scene.prototype.$ = function(selector)
	{
		if(typeof this.cached_selectors[selector] != "undefined")
		{
			return this.cached_selectors[selector];
		}
		else
		{
			list = this._SelectInstances(this, this.instances, selector);
			this.cached_selectors[selector] = list;
			return list;
		}
	}
	
	this.Scene.prototype._Draw = function()
	{
		this.draw_count += 1;
		
		if(this.canvas !== null)
		{
			var ctx = this.canvas.getContext("2d");
			ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
			
			for(i in this.instances)
			{
				this.instances[i]._Draw();
			}
		}
	}
	
	this.Scene.prototype._GetTextContext = function(options)
	{
		var weight = "normal", style = "normal", font = "sans-serif", size = 16, alpha = 1, scale = 1;
			
		if(typeof options.bold != "undefined" && options.bold)
		{
			weight = "bold";
		}
		
		if(typeof options.italic != "undefined" && options.italic)
		{
			style = "italic";
		}
		
		if(typeof options.size != "undefined")
		{
			size = options.size;
		}
		
		if(typeof options.font != "undefined")
		{
			font = options.font;
		}
		
		if(typeof options.alpha != "undefined")
		{
			alpha = options.alpha;
		}
		
		if(typeof options.scale != "undefined")
		{
			scale = options.scale;
		}
		
		var ctx = this.canvas.getContext("2d");
		
		ctx.save();
		
		ctx.font = weight + " " + style + " " + size + "px '" + font + "'";
		ctx.globalAlpha = alpha;
		ctx.scale(scale, scale);
		
		return ctx;
	}
	
	this.Scene.prototype._Initialize = function(event)
	{
		this.last_timestamp = this.date.getTime();
		this.SetInterval("_step", (1000/this.fps), this._Step);
	}
	
	this.Scene.prototype._ProcessClickEvent = function(event)
	{
		var coordinates = this.canvas.relativeCoordinates(event);
		
		var changed = this._BubbleEvent("_HandleClickEvent", {
			x: coordinates.x,
			y: coordinates.y,
			button: event.which
		}, this.$("/" + coordinates.x + "," + coordinates.y));
		
		if(changed == true)
		{
			this.dirty = true;
		}
	}
	
	this.Scene.prototype._ProcessMouseMoveEvent = function(event)
	{
		var coordinates = this.canvas.relativeCoordinates(event);
		this.mouse_coordinates = coordinates;
		this.mouse_moved = true;
	}
	
	this.Scene.prototype._SelectInstances = function(context, items, selector)
	{
		var segments = selector.split("/");
		segments.shift();
		
		methods = {
			"coordinate": {
				"regex": /^(!?)([0-9]+),([0-9]+)$/i,
				"action": function(context, items, match){
					var inverted = (match[1] == "!");
					var x = match[2];
					var y = match[3];
					
					return items.filter(function(object){
						var hit = false;
						
						if(object.draw_self === true)
						{
							var sprite = this._engine.GetSprite(object.sprite);
							
							if(x >= object.x && x < (object.x + sprite.width) && y > object.y && y < (object.y + sprite.height))
							{
								/* Initial region test succeeded.
								 * TODO: Add bounding rectangles. */
								var relative_x = x - object.x;
								var relative_y = y - object.y;
								
								if(object.precise_collision == false)
								{
									hit = true;
								}
								else
								{
									alpha = sprite.GetAlpha(relative_x, relative_y) / 255;
									
									if(alpha > object.collision_tolerance)
									{
										/* Alpha hit. */
										hit = true;
									}
								}
							}
						}
						
						if(inverted == true)
						{
							return !hit;
						}
						else
						{
							return hit;
						}
					});
				}
			},
			"object": {
				"regex": /^(!?)([a-z_][a-z0-9_+-]*)$/i,
				"action": function(context, items, match){
					inverted = (match[1] == "!");
					name = match[2];
					
					return items.filter(function(object){
						if(inverted)
						{
							return !(name == object.name);
						}
						else
						{
							return (name == object.name);
						}
					});
				}	
			}
		}
		
		for(i in segments)
		{
			var segment = segments[i];
			
			for(m in methods)
			{
				var method = methods[m];
				var match = method.regex.exec(segment);
				
				if(match != null)
				{
					items = method.action(context, items, match);
					break;
				}
			}
		}
		
		return items;
	}
	
	this.Scene.prototype._Step = function(event)
	{
		this.step_counter++;
		
		if(this.step_counter == this.fps)
		{
			this.step_counter = 0;
			
			var date = new Date;
			var current_timestamp = date.getTime()
			this.current_fps = (1000 / (current_timestamp - this.last_timestamp)) * this.fps;
			this.last_timestamp = current_timestamp;
		}
		
		if(this.mouse_moved == true)
		{
			this.dirty = this.CallEvent(this.OnMouseMove, this.mouse_coordinates) ? true : this.dirty;
			
			var select = function(prefix, coordinates)
			{
				var selector = "/" + prefix + coordinates.x + "," + coordinates.y;
				return this.$(selector);
			}.bind(this);
			
			this.dirty = this._BubbleEvent("_HandleMouseOutEvent", this.mouse_coordinates, select("!", this.mouse_coordinates)) ? true : this.dirty;
			this.dirty = this._BubbleEvent("_HandleMouseOverEvent", this.mouse_coordinates, select("", this.mouse_coordinates)) ? true : this.dirty;
			this.dirty = this._BubbleEvent("_HandleMouseMoveEvent", this.mouse_coordinates, this.$("/")) ? true : this.dirty;
			
			this.mouse_moved = false;
		}
		
		if(this.CallEvent(this.OnStep, {}) == true)
		{
			this.dirty = true;
		}
		
		if(this._BubbleEvent("_HandleStepEvent", {}, this.instances) == true)
		{
			this.dirty = true;
		}
			
		if(this.dirty == true)
		{
			this.dirty = false;
			this.cached_selectors = {};
			this._Draw();
		}
	}
	
	
	/**--------------------------------------**/
	/**                SOUNDS                **/
	/**--------------------------------------**/
	
	this.Sound = function(name, source)
	{
		if(typeof _engine.sounds[name] !== "undefined")
		{
			throw {
				name: "NameError",
				message: "A sound with the given name already exists."
			}
		}
		
		if(typeof _engine.resources[source] === "undefined")
		{
			throw {
				name: "ResourceError",
				message: "The specified resource does not exist."
			}
		}
		
		this._engine = _engine;
		this.source = this._engine.resources[source];
		this.name = name;
		
		this._engine.sounds[name] = this;
	}
	
	this.Sound.prototype.Play = function(options)
	{
		if(this._engine.sound_enabled == true)
		{
			this.source.play(options);
		}
	}
	
	
	/**--------------------------------------**/
	/**               OBJECTS                **/
	/**--------------------------------------**/
	
	this.Object = function(name, proto)
	{
		if(typeof _engine.objects[name] !== "undefined")
		{
			throw {
				name: "NameError",
				message: "An object with the given name already exists."
			}
		}
		
		/* Base settings */
		this._engine = _engine;
		this.name = "";
		
		/* Metrics */
		this.x = 0;
		this.y = 0;
		this.width = 0;
		this.height = 0;
		this.alpha = 1;
		
		/* Collision handling */
		this.collision_tolerance = 0.2;
		this.precise_collision = false;
		
		/* Internal variables */
		this.draw_self = (typeof proto.sprite !== "undefined");
		this.scene = null;
		this.last_moused_over = false;
		
		/* Create a constructor for the object. */
		var skeleton = new Function();
		$.extend(true, skeleton.prototype, this, this._engine.Base, proto);
		skeleton.prototype.name = name;
		
		/* Store constructor and return prototype. */
		this._engine.objects[name] = skeleton;
		return skeleton.prototype;
	}
	
	this.Object.prototype.CreateInstance = function(vars)
	{
		return this._engine.CreateInstance(this, vars);
	}
	
	this.Object.prototype.Destroy = function()
	{
		this.CallEvent(this.OnDestroy);
			
		if(this.scene != null)
		{
			var index = this.scene.instances.indexOf(this);
			
			if(index != -1)
			{
				this.scene.instances.splice(index, 1);
			}
		}
	}
	
	this.Object.prototype._Draw = function()
	{
		if(this.draw_self == true && typeof this.sprite !== "undefined")
		{
			if(typeof this._engine.sprites[this.sprite] === "undefined")
			{
				throw {
					name: "SpriteError",
					message: "The specified sprite does not exist."
				}
			}
			
			this._engine.sprites[this.sprite].Draw(this.scene.canvas, this);
		}
		
		this.CallEvent(this.OnDraw, {canvas: this.scene.canvas});
	}
	
	this.Object.prototype._HandleClickEvent = function(event)
	{
		var relative_x = event.x - this.x;
		var relative_y = event.y - this.y;
		
		events = {
			1: this.OnClick,
			2: this.OnMiddleClick,
			3: this.OnRightClick
		}
		
		func = events[event.button];
		
		this.CallEvent(func, {
			x: event.x,
			y: event.y,
			button: event.button,
			relative_x: relative_x,
			relative_y: relative_y
		});
		
		return true;
	}
	
	this.Object.prototype._HandleMouseMoveEvent = function(event)
	{
		this.CallEvent(this.OnMouseMove, {
			x: event.x,
			y: event.y
		});
		return true;
	}
	
	this.Object.prototype._HandleMouseOutEvent = function(event)
	{
		if(this.last_moused_over == true)
		{
			this.last_moused_over = false;
				
			this.CallEvent(this.OnMouseOut, {
				x: event.x,
				y: event.y
			});
			
			return true;
		}
	}
	
	this.Object.prototype._HandleMouseOverEvent = function(event)
	{
		if(this.last_moused_over == false)
		{
			var relative_x = event.x - this.x;
			var relative_y = event.y - this.y;
			
			this.last_moused_over = true;
				
			this.CallEvent(this.OnMouseOver, {
				x: event.x,
				y: event.y,
				relative_x: relative_x,
				relative_y: relative_y
			});
			
			return true;
		}
	}
	
	this.Object.prototype._HandleStepEvent = function(event)
	{
		if(typeof this.sprite != "undefined")
		{
			var sprite = this._engine.sprites[this.sprite];
			this.width = sprite.width;
			this.height = sprite.height;
		}
		
		return this.CallEvent(this.OnStep, event);
	}
	
	/**--------------------------------------**/
	/**               SPRITES                **/
	/**--------------------------------------**/
	
	this.Sprite = function(name, source, options)
	{
		if(typeof _engine.sprites[name] !== "undefined")
		{
			throw {
				name: "NameError",
				message: "A sprite with the given name already exists."
			}
		}
		
		if(typeof _engine.resources[source] === "undefined")
		{
			throw {
				name: "ResourceError",
				message: "The specified resource does not exist."
			}
		}
		
		this._engine = _engine;
		this.source = this._engine.resources[source];
		this.name = name;
		
		if(typeof options.tile_x !== "undefined" && typeof options.tile_y !== "undefined" && typeof options.tile_w !== "undefined" && typeof options.tile_h !== "undefined")
		{
			this.tile_x = options.tile_x;
			this.tile_y = options.tile_y;
			this.tile_w = options.tile_w;
			this.tile_h = options.tile_h;
			this.tile = true;
		}
		else if(typeof options.tile_x !== "undefined" || typeof options.tile_y !== "undefined" || typeof options.tile_w !== "undefined" || typeof options.tile_h !== "undefined")
		{
			throw {
				name: "SpriteError",
				message: "Only a part of the tile parameters were specified."
			}
		}
		else
		{
			this.tile = false;
		}
		
		/* Store image data for click events and collision detection. */
		var collision_canvas = document.createElement("canvas");
		var width, height;
		
		if(this.tile == false)
		{
			width = this.source.width;
			height = this.source.height;
		}
		else if(this.tile == true)
		{
			width = this.tile_w;
			height = this.tile_h;
		}
		
		collision_canvas.width = this.width = width;
		collision_canvas.height = this.height = height;
		
		this.Draw(collision_canvas, null, {x: 0, y: 0, alpha: 1});
		
		ctx = collision_canvas.getContext("2d");
		this.image_data = ctx.getImageData(0, 0, width, height);
		
		delete collision_canvas;
		
		/* Store in engine. */
		this._engine.sprites[name] = this;
	}
	
	this.Sprite.prototype.Draw = function(canvas, object, options)
	{
		ctx = canvas.getContext("2d");
		
		if((typeof object == "undefined" || object == null) && (typeof options == "undefined" || typeof options.x == "undefined" || typeof options.y == "undefined"))
		{
			throw {
				name: "DrawError",
				message: "No drawing coordinates were specified."
			}
		}
		else if(typeof object == "undefined" || object == null)
		{
			var x = options.x;
			var y = options.y;
		}
		else
		{
			var x = object.x;
			var y = object.y;
		}
		
		if(typeof options != "undefined" && typeof options.alpha != "undefined")
		{
			var alpha = options.alpha;
		}
		else if(typeof object != "undefined" && object != null)
		{
			var alpha = object.alpha;
		}
		else
		{
			throw {
				name: "DrawError",
				message: "No alpha value was specified."
			}
		}
		
		ctx.globalAlpha = alpha;
		
		if(this.tile == true)
		{
			ctx.drawImage(this.source, this.tile_x, this.tile_y, this.tile_w, this.tile_h, x, y, this.tile_w, this.tile_h);
		}
		else
		{
			ctx.drawImage(this.source, x, y);
		}
	}
	
	this.Sprite.prototype.GetAlpha = function(x, y)
	{
		var key = (((y * this.width) + x) * 4) + 3;
		return this.image_data.data[key];
	}
	
	/**--------------------------------------**/
	/**            ENGINE FUNCTIONS          **/
	/**--------------------------------------**/
	
	this.AddItems = function(items)
	{	
		if(typeof items.sprites != "undefined")
		{
			
			for(name in items.sprites)
			{
				if(typeof items.sprites[name] == "string")
				{
					/* Stand-alone sprite. */
					new _engine.Sprite(name, items.sprites[name], {});
				}
				else
				{
					/* Probably a tileset. */
					new _engine.Sprite(name, items.sprites[name], {
						tile_x: items.sprites[name].tile_x,
						tile_y: items.sprites[name].tile_y,
						tile_w: items.sprites[name].tile_w,
						tile_h: items.sprites[name].tile_h,
					});
				}
			}
		}
		
		if(typeof items.sounds != "undefined")
		{
			for(name in items.sounds)
			{
				new _engine.Sound(name, items.sounds[name]);
			}
		}
		
		if(typeof items.objects != "undefined")
		{
			for(name in items.objects)
			{
				new _engine.Object(name, items.objects[name]);
			}
		}
		
		if(typeof items.scenes != "undefined")
		{
			for(name in items.scenes)
			{
				new _engine.Scene(name, items.scenes[name]);
			}
		}
	}
	
	this.CreateInstance = function(object, vars)
	{
		this.instance_increment++;
		
		if(typeof data == "undefined")
		{
			var data = {id: this.instance_increment};
		}
		else
		{
			data.id = this.instance_increment;
		}
		
		if(typeof object == "string")
		{
			var instance = new this.objects[object]();
		}
		else
		{
			var skeleton = new Function();
			skeleton.prototype = object;
			var instance = new skeleton();
		}
		
		$.extend(true, instance, vars);
		
		/* Call creation event. */
		instance.CallEvent(instance.OnCreate, {});
		
		return instance;
	}
	
	this.GetObject = function(name)
	{
		return this.objects[name].prototype;
	}
	
	this.GetScene = function(name)
	{
		return this.scenes[name];
	}
	
	this.GetSound = function(name)
	{
		return this.sounds[name];
	}
	
	this.GetSprite = function(name)
	{
		return this.sprites[name];
	}
	
	/**--------------------------------------**/
	/**            STANDARD LIBRARY          **/
	/**--------------------------------------**/
	
	this.Math = {};
	
	this.Math.Abs = Math.abs;
	this.Math.Absolute = Math.abs;
	this.Math.Acos = Math.acos;
	this.Math.Arccosine = Math.acos;
	this.Math.Asin = Math.asin;
	this.Math.Arcsine = Math.asin;
	this.Math.Atan = Math.atan;
	this.Math.Arctangent = Math.atan;
	this.Math.Atan2 = Math.atan2;
	this.Math.Arctangent2 = Math.atan2;
	this.Math.Ceil = Math.ceil;
	this.Math.Ceiling = Math.ceil;
	this.Math.Cos = Math.cos;
	this.Math.Cosine = Math.cos;
	this.Math.Exp = Math.exp;
	this.Math.Floor = Math.floor;
	this.Math.Log = Math.log;
	this.Math.Logarithm = Math.log;
	this.Math.Min = Math.min;
	this.Math.Minimum = Math.min;
	this.Math.Max = Math.max;
	this.Math.Maximum = Math.max;
	this.Math.Pow = Math.pow;
	this.Math.Power = Math.pow;
	this.Math.Round = Math.round;
	this.Math.Sin = Math.sin;
	this.Math.Sine = Math.sin;
	this.Math.Sqrt = Math.sqrt;
	this.Math.SquareRoot = Math.sqrt;
	this.Math.Tan = Math.tan;
	this.Math.Tangent = Math.tan;
	
	this.Random = {};
	
	this.Random.Choose = function()
	{
		if(arguments.length == 0)
		{
			/* The user passed in nothing. Bail out. */
			throw {
				name: "ArgumentError",
				message: "No arguments were specified."
			}
		}
		else if(arguments.length == 1 && typeof arguments[0].length != "undefined")
		{
			/* The user passed in an array. */
			arguments = arguments[0];
		}
		
		return arguments[Math.floor(Math.random() * arguments.length)];
	}
	
	this.Random.Number = function(floor, ceiling, precision)
	{
		var floor = (typeof floor == "undefined") ?		0 		: floor;
		var ceiling = (typeof ceiling == "undefined") ?		1 		: ceiling;
		var precision = (typeof ceiling == "undefined") ?	0.00000001 	: precision;
		
		var base_number = Math.random();
		var width = Math.abs(ceiling - floor);
		var rounding_factor = 1 / precision;
		
		var multiplied = floor + (base_number * width);
		return Math.floor(multiplied * rounding_factor) / rounding_factor;
	}
	
	this.Random.Pick = function()
	{
		var chosen = [];
		var results = [];
		var _arguments = Array.prototype.slice.call(arguments);
		var count = _arguments.shift();
		
		if(arguments.length == 0)
		{
			/* The user passed in nothing. Bail out. */
			throw {
				name: "ArgumentError",
				message: "No arguments were specified."
			}
		}
		else if(_arguments.length == 1 && typeof _arguments[0].length != "undefined")
		{
			/* The user passed in an array. */
			_arguments = _arguments[0];
		}
		
		if(count > _arguments.length)
		{
			/* The user requested more items than exist in the arguments. */
			throw {
				name: "ArgumentError",
				message: "Not enough arguments were specified. The amount of specified items must be equal to or larger than the requested amount."
			}
		}
		
		for(var i = 0; i < count; i++)
		{
			var id = 0;
			
			do
			{
				id = Math.floor(Math.random() * _arguments.length);
			} while (chosen.indexOf(id) != -1)
			
			chosen.push(id);
			results.push(_arguments[id]);
		}
		
		return results;
	}
	
	this.Random.String = function(length, alphabet)
	{
		if(typeof alphabet == "undefined")
		{
			alphabet = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		}
		
		rand = "";
				
		for(i = 0; i < length; i++)
		{
			rand += alphabet[Math.floor(Math.random() * alphabet.length)];
		}
		
		return rand;
	}

	this.Draw = {};
	
	this.Draw.Text = function(x, y, text, options)
	{
		
	}
	
	this.Draw.Rectangle = function(x1, y1, x2, y2, options)
	{
		
	}
	
	this.Draw.Line = function(x1, y1, x2, y2, options)
	{
		
	}
	
	this.Draw.BoxEllipse = function(x1, y1, x2, y2, options)
	{
		var x = (x1 + x2) / 2;
		var y = (y1 + y2) / 2;
		var rx = (x2 - x1) / 2;
		var ry = (y2 - y1) / 2;
		
		this.RadiusEllipse(x, y, rx, ry, options);
	}
	
	this.Draw.RadiusEllipse = function(x, y, rx, ry, options)
	{
		var canvas = $("#gamecanvas")[0];
		var ctx = canvas.getContext("2d");
		ctx.beginPath();
		
		if(rx == ry)
		{
			/* Circle. */
			ctx.arc(x, y, rx, 0, 2 * Math.PI, false);
		}
		else
		{
			/* Ellipse. */
			var step = 0.1

			ctx.moveTo(x + rx, y);
			
			for (var i = 0; i < Math.PI * 2 + step; i += step)
			{
				ctx.lineTo(x + Math.cos(i) * rx, y + Math.sin(i) * ry);
			}
		}
		
		ctx.lineWidth = 1;
		ctx.strokeStyle = 'black';
		ctx.stroke();
	}
	
	this.Draw.BoxPolygon = function(x1, y1, x2, y2, sides, options)
	{
		
	}
	
	this.Draw.RadiusPolygon = function(x, y, radius, sides, options)
	{
		
	}
}
