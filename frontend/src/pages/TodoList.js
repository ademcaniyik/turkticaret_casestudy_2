import React, { useState } from 'react';
import { useSelector, useDispatch } from 'react-redux';
import { Box, Grid, Paper, TextField, Button, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Pagination, FormControl, InputLabel, Select, MenuItem, Chip, Typography, IconButton } from '@mui/material';
import { Search, Add, Edit, Delete } from '@mui/icons-material';
import { useNavigate } from 'react-router-dom';
import { deleteTodo } from '../features/todos/todosSlice';

const TodoList = () => {
  const dispatch = useDispatch();
  const todos = useSelector((state) => state.todos.items);

  const navigate = useNavigate();

  const [searchTerm, setSearchTerm] = useState('');
  const [statusFilter, setStatusFilter] = useState('');
  const [priorityFilter, setPriorityFilter] = useState('');
  const [page, setPage] = useState(1);
  const [sortField, setSortField] = useState('created_at');
  const [sortDirection, setSortDirection] = useState('desc');

  const filteredTodos = todos.filter(todo => {
    const matchesSearch = todo.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
      todo.description?.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesStatus = !statusFilter || todo.status === statusFilter;
    const matchesPriority = !priorityFilter || todo.priority === priorityFilter;
    return matchesSearch && matchesStatus && matchesPriority;
  });

  const sortedTodos = [...filteredTodos].sort((a, b) => {
    if (sortField === 'created_at') {
      return sortDirection === 'asc' ? new Date(a.created_at) - new Date(b.created_at) : new Date(b.created_at) - new Date(a.created_at);
    }
    return sortDirection === 'asc' ? a[sortField].localeCompare(b[sortField]) : b[sortField].localeCompare(a[sortField]);
  });

  const handlePageChange = (event, value) => {
    setPage(value);
  };

  const handleSort = (field) => {
    setSortField(field);
    setSortDirection(sortDirection === 'asc' ? 'desc' : 'asc');
  };

  return (
    <Box sx={{ p: 3 }}>
      <Typography variant="h4" gutterBottom>
        Todo Listesi
      </Typography>

      {/* Filters */}
      <Box sx={{ mb: 3 }}>
        <Grid container spacing={2}>
          <Grid item xs={12} sm={6}>
            <TextField
              fullWidth
              label="Ara"
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              InputProps={{
                startAdornment: <Search sx={{ mr: 1 }} />,
              }}
            />
          </Grid>

          <Grid item xs={12} sm={3}>
            <FormControl fullWidth>
              <InputLabel>Status</InputLabel>
              <Select
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
                label="Status"
              >
                <MenuItem value="">Tümü</MenuItem>
                <MenuItem value="pending">Bekleyen</MenuItem>
                <MenuItem value="in_progress">Devam Eden</MenuItem>
                <MenuItem value="completed">Tamamlanan</MenuItem>
              </Select>
            </FormControl>
          </Grid>

          <Grid item xs={12} sm={3}>
            <FormControl fullWidth>
              <InputLabel>Öncelik</InputLabel>
              <Select
                value={priorityFilter}
                onChange={(e) => setPriorityFilter(e.target.value)}
                label="Öncelik"
              >
                <MenuItem value="">Tümü</MenuItem>
                <MenuItem value="low">Düşük</MenuItem>
                <MenuItem value="medium">Orta</MenuItem>
                <MenuItem value="high">Yüksek</MenuItem>
              </Select>
            </FormControl>
          </Grid>
        </Grid>
      </Box>

      {/* Table */}
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell onClick={() => handleSort('title')}>
                Başlık
                {sortField === 'title' && (
                  sortDirection === 'asc' ? '↑' : '↓'
                )}
              </TableCell>
              <TableCell onClick={() => handleSort('status')}>
                Durum
                {sortField === 'status' && (
                  sortDirection === 'asc' ? '↑' : '↓'
                )}
              </TableCell>
              <TableCell onClick={() => handleSort('priority')}>
                Öncelik
                {sortField === 'priority' && (
                  sortDirection === 'asc' ? '↑' : '↓'
                )}
              </TableCell>
              <TableCell onClick={() => handleSort('due_date')}>
                Bitiş Tarihi
                {sortField === 'due_date' && (
                  sortDirection === 'asc' ? '↑' : '↓'
                )}
              </TableCell>
              <TableCell>İşlemler</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {sortedTodos.map((todo) => (
              <TableRow key={todo.id}>
                <TableCell>{todo.title}</TableCell>
                <TableCell>
                  <Chip
                    label={todo.status}
                    color={todo.status === 'completed' ? 'success' : todo.status === 'in_progress' ? 'warning' : 'default'}
                  />
                </TableCell>
                <TableCell>
                  <Chip
                    label={todo.priority}
                    color={todo.priority === 'high' ? 'error' : todo.priority === 'medium' ? 'warning' : 'default'}
                  />
                </TableCell>
                <TableCell>{todo.due_date ? new Date(todo.due_date).toLocaleDateString() : '-'}</TableCell>
                <TableCell>
                  <IconButton onClick={() => navigate(`/todos/${todo.id}`)}>
                    <Edit />
                  </IconButton>
                  <IconButton onClick={() => dispatch(deleteTodo(todo.id))}>
                    <Delete />
                  </IconButton>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>

      {/* Pagination */}
      <Box sx={{ mt: 3, display: 'flex', justifyContent: 'center' }}>
        <Pagination
          count={Math.ceil(filteredTodos.length / 10)}
          page={page}
          onChange={handlePageChange}
        />
      </Box>

      {/* Add Button */}
      <Button
        variant="contained"
        color="primary"
        startIcon={<Add />}
        onClick={() => navigate('/todos/new')}
        sx={{ mt: 3 }}
      >
        Yeni Todo Ekle
      </Button>
    </Box>
  );
};

export default TodoList;
