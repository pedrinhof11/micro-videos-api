import React from 'react';
import {TextField, FormControlLabel, Checkbox, Box, Button, ButtonProps, makeStyles, Theme} from "@material-ui/core";
import {useForm} from "react-hook-form";
import CategoryResource from "../../http/CategoryResource";

const useStyles = makeStyles((theme: Theme) => ({
  submit: {
    margin: theme.spacing(1)
  }
}))

const CategoriesForm = () => {

  const classes = useStyles();

  const buttonProps : ButtonProps = {
    variant: "contained",
    className: classes.submit
  }

  const {register, handleSubmit, getValues} = useForm({
    defaultValues: {
      is_active: true
    }
  });

  const onSubmit = async (formData: any, event?: React.BaseSyntheticEvent) => {
    try {
      const { data } = await CategoryResource.create(formData);
      console.log(data);
    } finally {

    }
  }

  const onSave = async () => onSubmit(getValues())

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <TextField
        inputRef={register}
        name="name"
        label="Nome"
        variant="outlined"
        fullWidth
      />
      <TextField
        inputRef={register}
        name="description"
        label="Descrição"
        variant="outlined"
        rows="4"
        margin="normal"
        multiline
        fullWidth
      />
      <FormControlLabel
        inputRef={register}
        control={<Checkbox name="is_active" defaultChecked color="primary" />}
        label="Ativo?"
        labelPlacement="end"
      />
      <Box dir="rtl">
        <Button color="primary" {...buttonProps} onClick={onSave}>Salvar</Button>
        <Button {...buttonProps} type="submit">Salvar e continuar editado</Button>
      </Box>
    </form>
  );
};

export default CategoriesForm;