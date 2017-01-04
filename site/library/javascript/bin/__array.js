/**
	* * Biblioth�ques de fonctions pour tableaux.
	* *
	* * @author Julien Fontanet
	* * @date 2007-04-25
	* * @version 0.4.4
**/

/**
	* * V�rifie si une variable est un tableau.
	* *
	* * @param mixed variable La variable � v�rifier.
	* * @return boolean true si variable est un tableau, false sinon.
**/
function isArray(variable)
{
	return variable instanceof Array;
}

/**
	* * Compare le tableau avec une variable de fa�on non r�cursive.
	* *
	* * @param mixed variable La variable � comparer avec le tableau.
	* * @return boolean true si ils sont �gaux, false sinon.
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
	* * Filtre un tableau � partir d'une fonction si elle est d�finie, efface les
	* * membres assimilables � false sinon.
	* *
	* * @param mixed callBack La fonction � utiliser pur le filtrage.
	* * @param boolean recursive true pour la r�cursivit�.
	* * @return Array Tableau filtr�.
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
	* * V�rifie si une valeur est pr�sente dans le tableau.
	* *
	* * @param mixed value La valeur � trouver.
	* * @param boolean recursive true pour la r�cursivit�.
	* * @return boolean true si la valeur a �t� trouv�, false sinon.
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
	* * Convertit le tableau en cha�ne.
	* *
	* * @return String Le tableau sous forme de cha�ne.
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

