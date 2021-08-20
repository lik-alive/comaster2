var FileManagerOptions = Object.freeze({'Upload':1, 'Multiple':2, 'OnlyPdf':3, 'Closeable':4});

class FileManager {
	
	constructor() {
		this.fmap = new Map();
		this.options = [];
		this.list = $("<div class='file-manager-list'></div>");
		for (var i = 0; i < arguments.length; i++) {
			this.options.push(arguments[i]);
		}
	}
	
	get filesCount() {
		return this.fmap.size;
	}
	
	get files() {
		return this.fmap.values();
	}
	
	get file() {
		return this.fmap.values().next().value;
	}
	
	embedObject(canvas) {
		canvas.addClass('file-manager');
		
		if (this.options.includes(FileManagerOptions.Upload)) this.embedFileUploadArea(canvas);
		
		canvas.append(this.list);
	}
	
	//Handle files-drop
	addFiles(files) {
		var length = files.length;
		if (!this.options.includes(FileManagerOptions.Multiple) && length > 1) length = 1;

		for (var i = 0; i < length; i++) {
			//Do not accept folders
			if (!files[i].name.includes('.')) continue;
		
			if (this.options.includes(FileManagerOptions.OnlyPdf) && !files[i].name.toLowerCase().endsWith('.pdf')) continue;
			if (typeof this.fmap.get(files[i].name.toLowerCase()) != 'undefined') continue;
			
			this.embedFileInfo(files[i]);
		}
	}
	
	//Clear all files
	clear() {
		this.fmap.clear();
		this.list.empty();
	}
	
	//Create area to drag-n-drop files
	embedFileUploadArea(canvas) {
		var object = this;
		var dragcounter = 0;
		
		canvas.on({
			dragenter: function(e) {
				e.stopPropagation(); 
				e.preventDefault();				
				if (dragcounter === 0) $(this).addClass('hovered');
				dragcounter++;
			},
			dragover: function(e) {
				e.stopPropagation(); 
				e.preventDefault();
			},
			dragleave: function(e) {
				e.stopPropagation(); 
				e.preventDefault();
				dragcounter--;
				if (dragcounter === 0) $(this).removeClass('hovered');
			},
			drop: function(e) {
				e.stopPropagation(); 
				e.preventDefault();
				dragcounter = 0;
				$(this).removeClass('hovered');
				object.addFiles(e.originalEvent.dataTransfer.files);
			}
		});
		
		var fileInput = $("<input class='file-manager-browse' type='file' />");
		fileInput.hide();
		if (this.options.includes(FileManagerOptions.Multiple)) fileInput.attr('multiple', true);
		fileInput.change(function() {
			object.addFiles(this.files);
		});
		
		var title = $("<div class='file-manager-utitle'>Перетащите файл или нажмите </div>");
		if (this.options.includes(FileManagerOptions.Multiple)) title.html('Перетащите файлы или нажмите ');
		
		var browse = $("<a>Обзор</a>");
		browse.click(function() {
			fileInput.click(); 
		});
		title.append(browse);
		
		canvas.append(fileInput);
		canvas.append(title);
	}
	
	//Create object representing a file
	embedFileInfo(file) {
		var fmap = this.fmap;
		
		var div = $("<div class='file-manager-finfo'></div>");
		var name = $("<p class='file-manager-fname'>" + file.name + "</p>");
		var length = $("<p class='file-manager-flength'>" + Math.round(file.size/1024/1024*100)/100 + ' MB' + "</p>");
		
		var closeButton = $("<button class='file-manager-fclose btn' type='button'><i class='fa fa-close'></i></button>");
		closeButton.click(function() {
			div.remove();
			fmap.delete(file.name.toLowerCase());
		});
		if (!this.options.includes(FileManagerOptions.Closeable)) closeButton.hide();
		
		div.append(name);
		div.append(length);
		div.append(closeButton);
		
		this.list.append(div);
		
		fmap.set(file.name.toLowerCase(), file);
		
		this.disMonitor(file, closeButton);
	}
	
	//Monitor for disappearing files
	disMonitor(file, closeButton) {
		var that = this;
		if (file.size === 0) closeButton.trigger('click');
		else setTimeout(function() {that.disMonitor(file, closeButton)}, 500);
	}
}