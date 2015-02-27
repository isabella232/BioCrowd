@extends('baseGameView')

@section('extraheaders')
	<script src="js_final/ct-annotate.js"></script>
	<script>
		/**
		 * Update annotation count and enable/disable submit button accordingly -- at least
		 * one annotation must be made (or the no-annotation check box must be clicked).
		 */
		function updateAnnotationCount() {
			var count = ct_annotate.getAnnotations().length;
			$('#nrTags').html(count);
			
			if((annotationForm.noCells.checked == false) && (count == 0)) {
				//If the "noCells" checkbox is unchecked and there are no annotations, disable the submit button
				document.getElementById("disabledSubmitButton").disabled = true;
			} else {
				document.getElementById("disabledSubmitButton").disabled = false;
			}
		}

		function updateShapeSelection() {
			if(annotationShapeForm.rectangle.checked == true){
				ct_annotate.changeDrawShape(true);
			} else if(annotationShapeForm.ellipse.checked == true) {
				ct_annotate.changeDrawShape(false);
			}
		}

		/**
		 * Prepare response to be submitted.
		 */
		function prepareResponse() {
			response = ct_annotate.getAnnotations();
			response = JSON.stringify(response);
			$('#response').val(response);
		}

		$(document).ready(function(){
			document.getElementById("disabledSubmitButton").disabled = true;

			// Prepare canvas for annotation using ct_annotate library
			canvas = document.getElementById('annotationCanvas');

			// Trigger our function when annotation takes place.
			canvas.addEventListener('annotationChanged', updateAnnotationCount, false);

			// Perhaps doRect, styleDrag, styleFixed should be loaded from DB ?
			doRect=false;		// Draw ellipses
			styleDrag='red';	// Use red lines while drawing
			styleFixed='yellow';// Use yellow lines for established annotations
			ct_annotate.loadCanvasImage(canvas, '{{ $image }}', doRect, styleDrag, styleFixed);
		});
	</script>
@stop

@section('gameForm')
							

						<div class="span7">
							<table>
							<tr><td>
								<canvas id="annotationCanvas"></canvas>
								</td>
								<td width="20px"></td>
							<td>
								<form name="annotationShapeForm" action="">
								<input type="radio" id="rectangle" name="shape" onClick="updateShapeSelection()" value="Rectangle">Rectangle<br/>
								<input type="radio" id="ellipse" name="shape" onClick="updateShapeSelection()" value="Ellipse" checked="checked">Ellipse
								</form>
							</td>
							</table>
						</div>
						<div class="span7">
							{{ Form::button('Remove last', ['onClick' => 'ct_annotate.removeLast()']) }}							
						</div>
						
						<div class="span4">
							<p>
							<div class="cml_field">
								<div class="border">
									Number of bounding boxes: <span class="nrTags" id="nrTags">0</span>
								</div>
							</div>
							<br>
							{{ Form::hidden('gameId', $gameId) }}
							{{ Form::hidden('taskId', $taskId) }}
							{{ Form::hidden('response','', [ 'id' => 'response' ] ) }}
							<div id="None of the above"> <!-- This statement can be used to check for spammers, so keep this open as an option when the rest is checked -->
								{{ Form::checkbox('noCells', 'true', false , [ 'id' => 'noCells', 'onClick' => 'updateAnnotationCount();' ]) }}
								{{ Form::label('noCells', $responseLabel) }}
							</div>
							<table width="100%">
								<tr><td align="center">{{ Form::submit('Submit', ['id' => 'disabledSubmitButton', 'onClick' => 'prepareResponse();' ]) }}</td></tr>
							</table>
						</div>


@stop

