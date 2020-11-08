import React from 'react';
import {Box, Fab} from '@material-ui/core';
import {Link} from 'react-router-dom';
import Page from '../../components/Page';
import AddIcon from "@material-ui/icons/Add";
import CastMembersTable from './CastMembersTable';

interface Props {
}

const CastMembers = (props: Props) => {
  return (
    <Page title="Listagem de Membros de Elenco">
    <Box dir="rtl">
      <Fab
        title="Adicionar Membro de Elenco"
        size="small"
        component={Link}
        to="/cast-members/create"
      >
        <AddIcon/>
      </Fab>
    </Box>
    <Box>
      <CastMembersTable />
    </Box>
  </Page>
  )
}

export default CastMembers
