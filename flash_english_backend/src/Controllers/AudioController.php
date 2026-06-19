<?php

namespace App\Controllers;

class AudioController
{
	public function download(int $categoryId, int $unitId): void
	{
		try {

			$fileName = sprintf(
				'unit_%02d_%02d.zip',
				$categoryId,
				$unitId
			);

			$filePath = BASE_PATH . '/storage/audio/' . $fileName;

			logger()->debug('Download audio zip: ' . $filePath);

			if (!file_exists($filePath)) {
				http_response_code(404);

				echo json_encode([
					'error' => 'Audio zip not found'
				]);

				return;
			}

			header('Content-Type: application/zip');
			header(
				'Content-Disposition: attachment; filename="' .
					$fileName .
					'"'
			);
			header(
				'Content-Length: ' .
					filesize($filePath)
			);

			readfile($filePath);
			logger()->debug(
				sprintf(
					'Download audio zip: %s (%d bytes)',
					$fileName,
					filesize($filePath)
				)
			);
			exit;
		} catch (\Exception $e) {

			logger()->error($e->getMessage());

			http_response_code(500);

			echo json_encode([
				'error' => $e->getMessage()
			]);
		}
	}
}
