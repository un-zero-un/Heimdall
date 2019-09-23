import {Action} from "redux";

export type DefaultState = {

};

const initialState: DefaultState = {

};

export default function(state: DefaultState = initialState, action: Action): DefaultState {
    switch (action.type) {
        default:
            return state;
    }
}
