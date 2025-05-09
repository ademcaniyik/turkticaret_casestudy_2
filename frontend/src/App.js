import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import { Provider } from 'react-redux';
import { store } from './store';

// Pages
import Dashboard from './pages/Dashboard';
import TodoList from './pages/TodoList';
import TodoDetail from './pages/TodoDetail';
import CategoryList from './pages/CategoryList';

// Components
import Layout from './components/Layout';
import ThemeSwitch from './components/ThemeSwitch';

const theme = createTheme({
  palette: {
    mode: 'light',
    primary: {
      main: '#1976d2',
    },
    secondary: {
      main: '#dc004e',
    },
  },
});

function App() {
  return (
    <Provider store={store}>
      <ThemeProvider theme={theme}>
        <CssBaseline />
        <Router>
          <Layout>
            <ThemeSwitch />
            <Routes>
              <Route path="/" element={<Dashboard />} />
              <Route path="/todos" element={<TodoList />} />
              <Route path="/todos/:id" element={<TodoDetail />} />
              <Route path="/categories" element={<CategoryList />} />
            </Routes>
          </Layout>
        </Router>
      </ThemeProvider>
    </Provider>
  );
}

export default App;
