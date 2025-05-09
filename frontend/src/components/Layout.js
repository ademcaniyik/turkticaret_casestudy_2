import React from 'react';
import { Box, Button } from '@mui/material';
import { useNavigate } from 'react-router-dom';

const Layout = ({ children }) => {
  const navigate = useNavigate();

  const menuItems = [
    { label: 'Dashboard', path: '/' },
    { label: 'Todo Listesi', path: '/todos' },
    { label: 'Kategoriler', path: '/categories' },
  ];

  return (
    <Box sx={{ display: 'flex', minHeight: '100vh' }}>
      {/* Sidebar */}
      <Box
        sx={{
          width: 240,
          flexShrink: 0,
          bgcolor: 'background.paper',
          borderRight: 1,
          borderColor: 'divider',
          p: 2,
        }}
      >
        {menuItems.map((item) => (
          <Button
            key={item.path}
            fullWidth
            onClick={() => navigate(item.path)}
            sx={{
              mb: 1,
              '&.MuiButton-root': {
                borderRadius: 0,
              },
              '&.MuiButton-root:hover': {
                bgcolor: 'action.hover',
              },
            }}
          >
            {item.label}
          </Button>
        ))}
      </Box>

      {/* Main Content */}
      <Box sx={{ flex: 1, p: 3 }}>
        {children}
      </Box>
    </Box>
  );
};

export default Layout;
