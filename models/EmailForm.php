<?php
class EmailForm extends Users{

	public function rules(){
		return [
			[['email'], 'required'],
		];
	}
}
