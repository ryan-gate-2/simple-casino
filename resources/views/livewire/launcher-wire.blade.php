	@if($viewType === 'error')
		<h3>Error: {{ $text }}</h3>
	@endif
	@if($viewType === 'gamelaunch')
		<div
		  class="embed-responsive embed-responsive-16by9 flex items-center justify-center relative w-full overflow-hidden"
		  style="padding-top: 56.25%"
		>
		  <iframe
		    class="embed-responsive-item absolute top-0 rounded-lg right-0 bottom-0 left-0 w-full h-full"
			src="{{ $url }}"
		    allowfullscreen=""
		    id="240632615"
		  ></iframe>
		</div>

	@endif
            
