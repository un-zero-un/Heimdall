import {Button, ButtonProps} from 'grommet';
import React from 'react';
import {useHistory} from 'react-router';

type Props = ButtonProps & {
    path: string,
};

export default function RoutedButton({path, ...props}: Props) {
    const history = useHistory();

    return (
        <Button href={path} as="a" {...props} onClick={(e: any) => {
            e.preventDefault();

            history.push(path);
        }}/>
    );
}
