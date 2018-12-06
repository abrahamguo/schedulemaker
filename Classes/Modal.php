	<?php
	/**
	 * Created by PhpStorm.
	 * User: abraham
	 * Date: 11/4/18
	 * Time: 1:27 PM
	 */

	class Modal {

		/**
		 * @type int
		 */
		private static $idCounter = 0;

		/**
		 * @type string
		 */
		private $title;

		/**
		 * @type string
		 */
		private $contents;

		/**
		 * @type string
		 */
		private $buttonText;

		/**
		 * @type string
		 */
		private $buttonClass;

		/**
		 * @var string
		 */
		private $buttonOtherClass;

		/**
		 * @var bool
		 */
		private $save = true;

		/**
		 * @type bool
		 */
		private $large = false;

		/**
		 * @type string
		 */
		private $id;

		/**
		 * @param mixed $title
		 * @return Modal
		 */
		public function title (string $title): Modal {
			$this->title = $title;
			return $this;
		}

		/**
		 * @param mixed $contents
		 * @return Modal
		 */
		public function contents (string $contents): Modal {
			$this->contents = $contents;
			return $this;
		}

		/**
		 * @param mixed $buttonText
		 * @return Modal
		 */
		public function buttonText (string $buttonText): Modal {
			$this->buttonText = $buttonText;
			return $this;
		}

		/**
		 * @return Modal
		 */
		public function noSave (): Modal {
			$this->save = false;
			return $this;
		}

		/**
		 * @param mixed $buttonClass
		 * @return Modal
		 */
		public function buttonClass (string $buttonClass): Modal {
			$this->buttonClass = $buttonClass;
			return $this;
		}

		public function buttonOtherClass (string $class): Modal {
			$this->buttonOtherClass = $class;
			return $this;
		}

		/**
		 * @return Modal
		 */
		public function large (): Modal {
			$this->large = true;
			return $this;
		}

		public function __construct () { $this->id = "modal-" . self::$idCounter++; }
		
		public function echo (): void {
			echo "
				<button type='button' class='btn btn-$this->buttonClass $this->buttonOtherClass' {$this->renderModalAttrs()}>
					$this->buttonText
				</button>
				{$this->renderModal()}
			";
		}

		public function renderModal (): string {
			return "
				<div class='modal fade' id='$this->id'>
					<div class='modal-dialog" . ($this->large ? " modal-lg" : "") . "'>
						<form class='modal-content'>
							<div class='modal-header'>
								<h5 class='modal-title'>$this->title</h5>
								<button type='button' class='close' data-dismiss='modal' title='Close'>&times;</button>
							</div><!-- /.modal-header -->
							<div class='modal-body'>$this->contents</div><!-- /.modal-body -->
							<div class='modal-footer'>
								<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
								" . ($this->save ? "<button type='submit' class='btn btn-primary'>Save changes</button>" : "") . "
							</div>
						</form>
					</div><!-- /.modal-dialog -->
				</div><!-- /.modal -->
			";
		}

		public function renderModalAttrs (): string {
			return " data-toggle='modal' data-target='#$this->id' ";
		}
	}