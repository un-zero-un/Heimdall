import {Heading, HeadingProps} from 'grommet';
import React, {ReactNode} from 'react';

type Props = {
    children: ReactNode,
    level?: HeadingProps['level'],
}

export default function Title({children, level = 2}: Props) {
    return <Heading level={level}>{children}</Heading>;
}
