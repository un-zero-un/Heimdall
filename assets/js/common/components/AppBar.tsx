import React from 'react';
import {Box, Heading} from 'grommet';
import {Link} from 'react-router-dom';

const styles = require('../../../css/common/AppBar.scss');

export default function AppBar() {
    return (
        <>
            <Box
                tag='header'
                direction='row'
                align='center'
                justify='between'
                background='brand'
                pad={{left: 'medium', right: 'small', vertical: 'small'}}
                elevation='medium'
                style={{zIndex: 1}}>
                <Heading margin="none" level={1} size="small">
                    <Link to="/" className={styles.AppBar__link}>Heimdall</Link>
                </Heading>
            </Box>
        </>
    );
}
