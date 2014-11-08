window.FW = {};
FW.EVENTS = {
	link: {
		click: function () {
			$('email').value = "admin@domain.com";
			$('password').value = "123";
			return false;
		}
	},

        password: {
            blur: function () {
		alert("blurred");
	    }
        },
	
	row_adder: {
		click: function () {
			var index = this.readAttribute('index');
			index = (!index ? null : parseInt(index));
			FW.add_row_to_table('mytable', index, {name: this.readAttribute('name'), email: this.readAttribute('email')});
			return false;
		}
	}
	
};
