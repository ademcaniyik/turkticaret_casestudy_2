import React from 'react';
import { useSelector } from 'react-redux';
import { Box, Grid, Paper, Card, CardContent, CardMedia, CardActions, Button, Typography } from '@mui/material';
import { useNavigate } from 'react-router-dom';

const Dashboard = () => {
  const todos = useSelector((state) => state.todos.items);
  const categories = useSelector((state) => state.categories.items);
  const navigate = useNavigate();

  const stats = {
    totalTodos: todos.length,
    completedTodos: todos.filter(todo => todo.status === 'completed').length,
    pendingTodos: todos.filter(todo => todo.status === 'pending').length,
    inProgressTodos: todos.filter(todo => todo.status === 'in_progress').length,
    highPriority: todos.filter(todo => todo.priority === 'high').length,
  };

  const upcomingTodos = todos
    .filter(todo => todo.due_date && new Date(todo.due_date) > new Date())
    .sort((a, b) => new Date(a.due_date) - new Date(b.due_date))
    .slice(0, 5);

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" gutterBottom>
        Dashboard
      </Typography>

      {/* Statistics Grid */}
      <Grid container spacing={3} sx={{ mb: 4 }}>
        <Grid item xs={12} sm={6} md={3}>
          <Paper sx={{ p: 2, textAlign: 'center' }}>
            <Typography variant="h6" color="primary">
              {stats.totalTodos}
            </Typography>
            <Typography variant="subtitle1">Toplam Todo</Typography>
          </Paper>
        </Grid>
        <Grid item xs={12} sm={6} md={3}>
          <Paper sx={{ p: 2, textAlign: 'center' }}>
            <Typography variant="h6" color="success">
              {stats.completedTodos}
            </Typography>
            <Typography variant="subtitle1">Tamamlanan</Typography>
          </Paper>
        </Grid>
        <Grid item xs={12} sm={6} md={3}>
          <Paper sx={{ p: 2, textAlign: 'center' }}>
            <Typography variant="h6" color="warning">
              {stats.pendingTodos}
            </Typography>
            <Typography variant="subtitle1">Bekleyen</Typography>
          </Paper>
        </Grid>
        <Grid item xs={12} sm={6} md={3}>
          <Paper sx={{ p: 2, textAlign: 'center' }}>
            <Typography variant="h6" color="error">
              {stats.highPriority}
            </Typography>
            <Typography variant="subtitle1">Yüksek Öncelik</Typography>
          </Paper>
        </Grid>
      </Grid>

      {/* Upcoming Todos */}
      <Paper sx={{ p: 3 }}>
        <Typography variant="h6" gutterBottom>
          Yaklaşan Todo'lar
        </Typography>
        <Box sx={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
          {upcomingTodos.map((todo) => (
            <Card key={todo.id}>
              <CardMedia
                sx={{
                  height: 140,
                  background: `linear-gradient(45deg, ${todo.priority === 'high' ? '#ff9800' : '#4caf50'} 30%, ${todo.priority === 'high' ? '#f57c00' : '#8bc34a'} 90%)`,
                }}
              />
              <CardContent>
                <Typography gutterBottom variant="h6" component="div">
                  {todo.title}
                </Typography>
                <Typography variant="body2" color="text.secondary">
                  {todo.description}
                </Typography>
              </CardContent>
              <CardActions>
                <Button size="small" onClick={() => navigate(`/todos/${todo.id}`)}>
                  Detay
                </Button>
              </CardActions>
            </Card>
          ))}
        </Box>
      </Paper>

      {/* Categories */}
      <Paper sx={{ p: 3, mt: 4 }}>
        <Typography variant="h6" gutterBottom>
          Kategoriler
        </Typography>
        <Grid container spacing={2}>
          {categories.map((category) => (
            <Grid item xs={12} sm={6} md={4} key={category.id}>
              <Paper
                sx={{
                  p: 2,
                  bgcolor: category.color,
                  color: 'white',
                  height: '100%',
                  display: 'flex',
                  flexDirection: 'column',
                  justifyContent: 'space-between',
                }}
              >
                <Typography variant="h6" gutterBottom>
                  {category.name}
                </Typography>
                <Typography variant="body2">
                  {category.description}
                </Typography>
                <Box sx={{ mt: 2 }}>
                  <Typography variant="subtitle1">
                    {category.todo_count || 0} Todo
                  </Typography>
                </Box>
              </Paper>
            </Grid>
          ))}
        </Grid>
      </Paper>
    </Box>
  );
};

export default Dashboard;
