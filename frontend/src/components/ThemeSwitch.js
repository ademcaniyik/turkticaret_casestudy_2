import React from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { IconButton } from '@mui/material';
import { Brightness4, Brightness7 } from '@mui/icons-material';
import { setTheme } from '../features/theme/themeSlice';

const ThemeSwitch = () => {
  const dispatch = useDispatch();
  const themeMode = useSelector((state) => state.theme.mode);

  const toggleTheme = () => {
    dispatch(setTheme(themeMode === 'light' ? 'dark' : 'light'));
  };

  return (
    <IconButton onClick={toggleTheme} color="inherit">
      {themeMode === 'dark' ? <Brightness7 /> : <Brightness4 />}
    </IconButton>
  );
};

export default ThemeSwitch;
