import React from 'react';
import {useLogout} from '../JWTProvider';

export default function LogoutButton() {
    const logout = useLogout();

    return (
        <button onClick={logout}>
            Logout
        </button>
    );
}
