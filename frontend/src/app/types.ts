type Language = {
	name: string;
	id: string;
};

type Term = {
	name: string;
	type: string;
	id: string;
};

type Err = {
	["key"]: any;
};

type ServerResponse<T> = {
	success: boolean;
	data?: T;
	error?: Err;
};

export {
	Language, Term, ServerResponse
};