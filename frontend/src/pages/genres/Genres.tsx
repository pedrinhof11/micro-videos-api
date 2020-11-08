import React from 'react'
import { Box, Fab } from '@material-ui/core'
import { Link } from 'react-router-dom'
import Page from '../../components/Page'
import AddIcon from "@material-ui/icons/Add";
import GenresTable from './GenresTable';

interface Props {
    
}

const Genres = (props: Props) => {
  return (
    <Page title="Listagem de Gêneros">
    <Box dir="rtl">
      <Fab
        title="Adicionar Gênero"
        size="small"
        component={Link}
        to="/genres/create"
      >
        <AddIcon/>
      </Fab>
    </Box>
    <Box>
      <GenresTable />
    </Box>
  </Page>
  )
}

export default Genres
