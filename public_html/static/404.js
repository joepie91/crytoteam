var engine;

$(function(){
	engine = new Engine({
		"enable_sound": false
	});
	
	loader = new engine.Preloader();
	loader.Run({
		onfinish: InitializeEffect
	});
});

function InitializeEffect()
{
	engine.AddItems({
		objects: {
			controller: {
				grid_w: 60,
				grid_h: 18,
				tile_w: 16,
				tile_h: 16,
				numbers: [],
				colored: [],
				OnCreate: function(event){
					for(var x = 0; x < this.grid_w; x++)
					{
						this.numbers[x] = [];
						this.colored[x] = [];
					}
					
					this.RegenerateNumbers();
				},
				OnMouseMove: function(event){
					this.SetMouse(event.x, event.y);
				},
				OnStep: function(event){
					if(this.scene.step_counter % 15 == 0)
					{
						this.RegenerateNumbers();
						return true;
					}
				},
				OnDraw: function(event){
					for(var x = 0; x < this.grid_w; x++)
					{
						for(var y = 0; y < this.grid_h; y++)
						{
							if(this.colored[x][y])
							{
								//var text = (this.colored[x][y]) ? "0" : this.numbers[x][y];
								var text = this.numbers[x][y];
								
								this.scene.DrawText(text, {
									x: x * this.tile_w,
									y: y * this.tile_h,
									color: (this.colored[x][y]) ? "blue" : "silver"
								});
							}
						}
					}
				},
				RegenerateNumbers: function(){
					for(var x = 0; x < this.grid_w; x++)
					{
						for(var y = 0; y < this.grid_h; y++)
						{
							this.numbers[x][y] = engine.Random.Choose(0, 1);
							this.colored[x][y] = false;
						}
					}
					
					this.ApplyMouse();
				},
				SetMouse: function(x, y){
					this.mouse_x = x;
					this.mouse_y = y;
					this.ApplyMouse();
				},
				ApplyMouse: function(){
					this.ClearColored();
					
					var matrix_w = 9;
					var matrix_h = 14;
					var matrix = [
						[0, 1, 1, 1, 1, 1, 1, 1, 1],
						[0, 0, 1, 1, 1, 1, 1, 1, 1],
						[0, 0, 0, 1, 1, 1, 1, 1, 1],
						[0, 0, 0, 0, 1, 1, 1, 1, 1],
						[0, 0, 0, 0, 0, 1, 1, 1, 1],
						[0, 0, 0, 0, 0, 0, 1, 1, 1],
						[0, 0, 0, 0, 0, 0, 0, 1, 1],
						[0, 0, 0, 0, 0, 0, 0, 0, 1],
						[0, 0, 0, 0, 0, 1, 1, 1, 1],
						[0, 0, 1, 0, 0, 1, 1, 1, 1],
						[0, 1, 1, 1, 0, 0, 1, 1, 1],
						[1, 1, 1, 1, 0, 0, 1, 1, 1],
						[1, 1, 1, 1, 1, 0, 0, 1, 1],
						[1, 1, 1, 1, 1, 0, 0, 1, 1]
					];
					
					for(var my = 0; my < matrix_h; my++)
					{
						for(var mx = 0; mx < matrix_w; mx++)
						{
							if(matrix[my][mx] == 0)
							{
								var target_x = Math.round(this.mouse_x / this.tile_w + mx);
								var target_y = Math.round(this.mouse_y / this.tile_h + my) + 1;
								
								//console.log(target_x, target_y);
								
								if(target_x >= 0 && target_x < this.grid_w && target_y >= 0 && target_y < this.grid_h)
								{
									this.colored[target_x][target_y] = true;
								}
							}
						}
					}
					
					if(this.scene)
					{
						this.scene.Redraw();
					}
				},
				ClearColored: function(){
					for(var x = 0; x < this.grid_w; x++)
					{
						for(var y = 0; y < this.grid_h; y++)
						{
							this.colored[x][y] = false;
						}
					}
				}
			}
		},
		scenes: {
			main: {
				width: 960,
				height: 288,
				fps: 30,
				OnLoad: function(event){
					this.Add("controller");
				},
				OnMouseMove: function(event){
					
				}
			}
		}
	});
	
	var canvas = $("#404canvas")[0];
	engine.GetScene("main").Attach(canvas);
	
	$("#404canvas").show();
}
