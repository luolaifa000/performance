<?php 
namespace Langyi\Performance\Helpers\Concerns;

use Langyi\Performance\Helpers\StackFrame;

trait ResolvesViewName
{
	public function resolveViewName()
	{
		$viewFrame = $this->first(function ($frame) {
			return preg_match('#^/storage/framework/views/[a-z0-9]+\.php$#', $frame->shortPath);
		});

		if (! $viewFrame) return $this;

		$renderFrame = $this->first(function ($frame) {
			return $frame->call == 'Illuminate\View\View->getContents()'
				&& $frame->object instanceof \Illuminate\View\View;
		});

		if (! $renderFrame) return $this;

		$resolvedViewFrame = new StackFrame(
			[ 'file' => $renderFrame->object->getPath(), 'line' => $viewFrame->line ],
			$this->basePath,
			$this->vendorPath
		);

		array_splice($this->frames, array_search($viewFrame, $this->frames), 0, [ $resolvedViewFrame ]);

		return $this;
	}
}
