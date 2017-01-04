/**
	* * Bibliothèques de fonctions pour tableaux.
	* *
	* * @author Julien Fontanet
	* * @date 2007-04-25
	* * @version 0.4.4
**/

/**
	* * Vérifie si une variable est un tableau.
	* *
	* * @param mixed variable La variable à vérifier.
	* * @return boolean true si variable est un tableau, false sinon.
**/
function isArray(variable)
{
	return variable instanceof Array;
}

/**
	* * Compare le tableau avec une variable de façon non récursive.
	* *
	* * @param mixed variable La variable à comparer avec le tableau.
	* * @return boolean true si ils sont égaux, false sinon.
**/
Array.prototype.compare = function (variable)
{
	if (!isArray(variable))
		return false;
	var len = this.length;
	if (variable.length != len)
		return false;
	var i = 0;
	while (i < len && this[i] == variable[i])
		++i;
	return i == len;
}

/**
	* * Filtre un tableau.
	* *
	* * Filtre un tableau à partir d'une fonction si elle est définie, efface les
	* * membres assimilables à false sinon.
	* *
	* * @param mixed callBack La fonction à utiliser pur le filtrage.
	* * @param boolean recursive true pour la récursivité.
	* * @return Array Tableau filtré.
**/
Array.prototype.filter = function (callBack, recursive)
{
	if (typeof callBack != "function")
	{
		callBack = function(value)
		{
			return Boolean(value) && (!isArray(value) || value.length != 0);
		}
	}
	if (typeof recursive != "boolean")
		recursive = Boolean(recursive);
	var	i = 0,
		len = this.length,
		result = new Array(),
		buffer;
	while (i < len)
	{
		buffer = this[i];
		if (recursive && isArray(buffer))
			buffer = buffer.filter(callBack, recursive);
		if (callBack(buffer))
			result.push(buffer);
		++i;
	}
	return result;
}


/**
	* * Vérifie si une valeur est présente dans le tableau.
	* *
	* * @param mixed value La valeur à trouver.
	* * @param boolean recursive true pour la récursivité.
	* * @return boolean true si la valeur a été trouvé, false sinon.
**/
Array.prototype.inArray = function (value, recursive)
{
	var i, current;
	if (typeof recursive != "boolean")
		recursive = Boolean(recursive);
	for (i = 0; i < this.length; ++i)
	{
		current = this[i];
		if (isArray(current) && recursive && current.inArray(value, recursive))
			return true;
		if (current == value)
			return true;
	}
	return false;
}


/**
	* * Convertit le tableau en chaîne.
	* *
	* * @return String Le tableau sous forme de chaîne.
**/
Array.prototype.toString = function ()
{
	var	i = 0,
		len = this.length,
		buffer = new Array(len);
	while (i < len)
	{
		if ((this[i] instanceof String) || (typeof this[i] == "string"))
			buffer[i] = '"' + this[i] + '"';
		else
		{
			if (isArray(this[i]))
				buffer[i] = this[i].toString();
			else
				buffer[i] = this[i];
		}
		++i;
	}
	return "Array(" + len + ") {" + buffer.join(", ") + "}";
}

